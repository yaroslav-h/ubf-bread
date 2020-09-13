
export const getUsernames2Ids = string => {
  const userIdFromMention = new RegExp('@([A-Za-z][A-Za-z0-9]+\\.?[A-Za-z0-9]+)\\(([0-9]+)\\)', 'g')

  const usernames2ids = {}
  let foundMention = null

  while ((foundMention = userIdFromMention.exec(string)) !== null) {
    if (foundMention[2] > 0) {
      usernames2ids[foundMention[1]] = foundMention[2]
    }
  }

  return usernames2ids
}

export const getVideoUrls = string => {
  const regExpVideos = [
    /^.*(?:(?:v|vi|be|videos|embed)\/(?!videoseries)|(?:v|ci)=)([\w-]{11})/i,
    /^.*vimeo.com\/(\d+)($|\/|\b)/i,
    /^.*(?:\/video|dai.ly)\/([A-Za-z0-9]+)([^#&?]*).*/i,
    /^.*coub.com\/(?:embed|view)\/([A-Za-z0-9]+)([^#&?]*).*/i
  ]

  const urls = []

  const { links } = getMatches(string)

  links.forEach(link => {
    for (const reg of regExpVideos) {
      if (reg.exec(link.value)) {
        urls.push(link.value)
      }
    }
  })

  return urls
}

export const getMatches = string => {
  const regexHashtag = new RegExp('#[a-zA-Z0-9_]{1,32}', 'g')
  const regexMention = new RegExp('@[A-Za-z][A-Za-z0-9]+\\.?[A-Za-z0-9]+\\([0-9]+\\)', 'g')
  const regexLink = new RegExp('https?://[^\\s/$.?#].[^\\s]*', 'g')

  const hashtags = []; const mentions = []; const links = []

  let foundHashtag = null; let foundMention = null; let foundLink = null

  while ((foundHashtag = regexHashtag.exec(string)) !== null) {
    hashtags.push({ value: foundHashtag[0].substr(1), index: foundHashtag.index, length: foundHashtag[0].length })
  }

  while ((foundMention = regexMention.exec(string)) !== null) {
    const userIdFromMention = new RegExp('@([A-Za-z][A-Za-z0-9]+\\.?[A-Za-z0-9]+)\\(([0-9]+)\\)', 'g')

    const mention = userIdFromMention.exec(foundMention[0])

    mentions.push({
      value: mention[1],
      user_id: mention[2],
      index: foundMention.index,
      length: foundMention[0].length
    })
  }

  while ((foundLink = regexLink.exec(string)) !== null) {
    links.push({ value: foundLink[0], index: foundLink.index, length: foundLink[0].length })
  }

  return {
    hashtags,
    mentions,
    links
  }
}

function breakTextByLine (text, maxLength = 70) {
  let lines = []

  const splited = text.split('\n')

  splited.forEach((line, index) => {
    if (line === '' && index > 0 && splited[index - 1] === '') {
      // return;
    }

    const hasNL = splited.length > index + 1

    if (line.length < maxLength) {
      lines.push(line + (hasNL ? '\n' : ''))
    } else {
      const subLines = line.match(/.{1,70}/g)

      subLines[subLines.length - 1] += (hasNL ? '\n' : '')

      lines = [...lines, ...subLines]
    }
  })

  return lines
}

export const getChunks = text => {
  if (text == null || `${text}`.length === 0) {
    return []
  }

  const matches = getMatches(text); const matches_array = []

  matches.hashtags.forEach(item => matches_array.push({
    type: 'hashtag', value: item.value, indices: [item.index, item.index + item.length]
  }))

  matches.mentions.forEach(item => matches_array.push({
    type: 'mention',
    value: item.value,
    indices: [item.index, item.index + item.length],
    user_id: item.user_id
  }))

  matches.links.forEach(item => matches_array.push({
    type: 'link', value: item.value, indices: [item.index, item.index + item.length]
  }))

  /// ///////

  const resultArr = []
  let beginIndex = 0

  matches_array.sort((a, b) => a.indices[0] - b.indices[0])

  matches_array.forEach(entity => {
    const chunk = text.substring(beginIndex, entity.indices[0])

    breakTextByLine(chunk).forEach(ln => {
      resultArr.push({ type: 'line', value: ln })
    })

    if (entity.type === 'hashtag') {
      resultArr.push({ type: 'hashtag', value: entity.value })
    } else if (entity.type === 'mention') {
      resultArr.push({ type: 'mention', value: entity.value, user_id: entity.user_id })
    } else if (entity.type === 'link') {
      if (entity.value.substr(0, 5) !== 'https') {
        resultArr.push({ type: 'text', value: '(insecure link)' })
      } else {
        resultArr.push({
          type: 'link',
          value: entity.value.substr(8, 50) + (entity.value.length > 58 ? '...' : ''),
          url: entity.value
        })
      }
    }

    beginIndex = entity.indices[1]
  })

  const chunk = text.substring(beginIndex, text.length)

  breakTextByLine(chunk).forEach(ln => {
    resultArr.push({ type: 'line', value: ln })
  })

  return resultArr
}

export const countLines = text => {
  return getChunks(text).filter(i => i.type === 'line').length
}
