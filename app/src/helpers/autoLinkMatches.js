
// const matches = [
//     { hashtag: "tag", indices: [7,11]},
//     { hashtag: "tag1", indices: [12,17]},
//     { hashtag: "tag", indices: [29,33]},
//
//     { mention: "yaris.dev", indices: [37,47]},
//
//     { url: "https://www.youtube.com/watch?v=GNFtQFS5cJc", link: "youtube.com/watch?v=GNFtQF…", indices: [60,103]},
// ];

function linkToHashtag (entity, text, options) {
  // const hash = text.substring(entity.indices[0], entity.indices[0] + 1);
  // <router-link to="/search/tags/$2">$1</router-link>
  return '<router-link to="/search/tags/' + entity.hashtag + '">' + '#' + entity.hashtag + '</router-link>'
}
function linkToMention (entity, text, options) {
  // const hash = text.substring(entity.indices[0], entity.indices[0] + 1);
  // <router-link to="/profile/$2">$1</router-link>
  return '<router-link to="/u/' + entity.mention + '">' + '@' + entity.mention + '</router-link>'
}
function linkToUrl (entity, text, options) {
  // const hash = text.substring(entity.indices[0], entity.indices[0] + 1);

  return '<a href="' + entity.url + "\" target='_blank'>" + entity.link + '</a>'
}

export default function (text, matches /* options */) {
  // let result = '';
  const resultArr = []
  let beginIndex = 0
  const entities = Array.from(matches)

  entities.sort((a, b) => a.i[0] - b.i[0])

  for (let i = 0; i < entities.length; i++) {
    const entity = entities[i]

    const chunk = text.substring(beginIndex, entity.i[0])

    // result += chunk;
    resultArr.push({ type: 'text', value: chunk })

    if (entity.t === 1) {
      // result += linkToHashtag(entity, text, {});
      resultArr.push({ type: 'hashtag', value: entity.v })
    } else if (entity.t === 2) {
      // result += linkToMention(entity, text, {});
      resultArr.push({ type: 'mention', value: entity.v })
    } else if (entity.t === 3) {
      // result += linkToUrl(entity, text, {});
      resultArr.push({ type: 'link', value: entity.v, url: entity.v })
    }

    beginIndex = entity.i[1]
  }

  const chunk = text.substring(beginIndex, text.length)
  // result += chunk;

  resultArr.push({ type: 'text', value: chunk })

  // console.log(result)

  return resultArr
}
