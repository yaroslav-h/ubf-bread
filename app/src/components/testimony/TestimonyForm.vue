<template>
  <form class="bg-white">{{contentJson}}
      <editor-content :editor="editor" />
  </form>
</template>

<script>
import { Editor, EditorContent } from 'tiptap'
import {
  Blockquote,
  CodeBlock,
  HardBreak,
  Heading,
  OrderedList,
  BulletList,
  ListItem,
  TodoItem,
  TodoList,
  Bold,
  Code,
  Italic,
  Link,
  History
} from 'tiptap-extensions'

export default {
  name: 'TestimonyForm',
  components: { EditorContent },
  props: {
    value: {
      required: true
    }
  },
  data () {
    const self = this
    return {
      editor: new Editor({
        onUpdate ({ getJSON }) {
          console.log(getJSON())
          self.$emit('input', getJSON())
        },
        extensions: [
          new Blockquote(),
          new BulletList(),
          new CodeBlock(),
          new HardBreak(),
          new Heading({ levels: [1, 2, 3] }),
          new ListItem(),
          new OrderedList(),
          new TodoItem(),
          new TodoList(),
          new Bold(),
          new Code(),
          new Italic(),
          new Link(),
          new History()
        ],
        content: ''
      })
    }
  },
  watch: {
    contentJson: {
      immediate: true,
      handler (value) {
        if (this.editor && value !== this.contentJson) {
          this.editor.setContent(value)
        }
      }
    }
  },
  beforeDestroy () {
    // Always destroy your editor instance when it's no longer needed
    this.editor.destroy()
  }
}
</script>

<style scoped>

</style>
