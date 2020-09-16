<template>
  <div>
    <editor-content class="editor__content" :editor="editor"/>
  </div>
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
  name: 'TestimonyBodyEditor',
  components: { EditorContent },
  props: {
    value: {
      required: true
    }
  },
  data () {
    return {
      editor: null
    }
  },
  watch: {
    value (value) {
      if (this.editor && value !== this.value) {
        this.editor.setContent(value)
      }
    }
  },
  beforeDestroy () {
    if (this.editor) {
      this.editor.destroy()
    }
  },
  mounted () {
    this.editor = new Editor({
      onUpdate: ({ getHTML }) => {
        this.$emit('input', getHTML())
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
      content: this.value
    })
  }
}
</script>
