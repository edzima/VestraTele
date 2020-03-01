<template>
  <div v-if="date" class="popup">
    <div class="modal">
      <button class="close" @click="handleClose">X</button>
      <div class="activeNotes">
        <h1>{{dateFormated}}</h1>
        <h3>aktywne notatki:</h3>
        <div class="notes">
          <div
            v-for="note in dayNotes"
            :key="note.id"
            class="note"
          >
            <div v-if="isEditable(note.id)" class="controls">
              <img src="../assets/discard.png" @click="discardNoteChanges(note.id)" />
              <img src="../assets/save.png" @click="saveEditNoteText(note.id)" />
            </div>
            <div v-else class="controls">
              <img src="../assets/edit.png" @click="allowEdit(note.id)" />
              <img src="../assets/delete.png" @click="deleteNote(note.id)" />
            </div>
            <textarea @change="editText" :value="note.title" :disabled="noteAllowEditId !== note.id" />
          </div>
        </div>
      </div>
      <div class="addNote">
        <h3>dodaj notatkę</h3>
        <textarea v-model="newNoteText" />
        <button @click="addNote">Dodaj</button>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { Vue, Prop, Component } from 'vue-property-decorator'
import { prettyDate, isSameDate } from '@/helpers/dateHelper'
@Component({})
export default class NewNotePopup extends Vue {
  @Prop({
    required: false
  })
  private date!: Date;

  @Prop({
    required: false
  })
  private allNotes!: Array<any>;

  private newNoteText = '';
  private editNoteText = '';
  private oldNoteText = '';
  private noteAllowEditId = 0;

  private handleClose () {
    this.$emit('close')
  }

  private deleteNote (noteID: number) {
    this.$emit('deleteNote', noteID)
  }

  private addNote () {
    const noteWithoutSpaces = this.newNoteText
      .split('')
      .filter(char => char !== ' ')
    if (!noteWithoutSpaces.length) return
    this.$emit('addNote', this.newNoteText.trim(), this.date)
    this.newNoteText = ''
  }

  private editText (e) {
    this.editNoteText = e.target.value
  }

  private allowEdit (noteId: number): void {
    this.noteAllowEditId = noteId
  }

  private isEditable (noteId: number): boolean {
    return this.noteAllowEditId === noteId
  }

  private discardNoteChanges () {
    this.editNoteText = this.oldNoteText
    this.noteAllowEditId = 0
  }

  private saveEditNoteText (noteID: number): void {
    const text = this.editNoteText.trim()
    const oldText = this.oldNoteText
    if (text === oldText) {
      return this.discardNoteChanges()
    }
    this.noteAllowEditId = 0
    this.$emit('editNoteText', noteID, text)
  }

  get dayNotes () {
    return this.allNotes.filter(note => isSameDate(note.date, this.date))
  }

  get dateFormated () {
    return prettyDate(this.date)
  }
}
</script>

<style scoped lang='less'>
.popup {
  position: absolute;
  width: 100vw;
  height: 100vh;
  z-index: 100;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  .modal {
    position: relative;
    width: 50vw;
    min-height: 40vh;
    background-color: white;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.6);
    border-radius: 10px;
    padding: 20px;
    border: 4px solid #ffffff59;
    button.close {
      position: absolute;
      top: 0;
      right: 0;
      border-radius: 0 10px 0 0;
      border: none;
      font-size: 23px;
      color: red;
      cursor: pointer;
    }
    h1 {
      text-align: center;
      color: #03a9f4;
      font-size: 38px;
    }
    h3 {
      text-align: left;
    }
    .activeNotes {
      .notes {
        .note {
          margin: 1vh 0;
          padding: 6px;
          min-height: 7vh;
          border: solid 1px rgba(165, 165, 165, 0.336);
          border-radius: 5px;
          box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
          position: relative;
          textarea {
            font-size: 15px;
            width: 90%;
            height: 6vh;
            resize: none;
            border: solid 1px #03a9f4;
          }

          textarea:disabled {
            border: none;
          }
          .controls {
            position: absolute;
            top: 10%;
            right: 0;
            // background-color: red;
            // width: 20%;
            height: 2.5vh;
            display: flex;
            flex-direction: row;
            justify-content: flex-end;
            align-items: center;
            img {
              height: 100%;
              margin-right: 10%;
              cursor: pointer;
            }
          }
        }
      }
    }
    .addNote {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      h3 {
        text-align: left;
        align-self: flex-start;
      }
      textarea {
        width: 97%;
        border: solid 2px #03a9f4;
        border-radius: 10px;
        height: 10vh;
        font-size: 17px;
        padding: 10px;
      }
      button {
        margin-left: auto;
        margin-top: 10px;
        border-radius: 10px;
        color: white;
        border: none;
        background-color: #03a9f4;
        font-size: 15px;
        padding: 10px 20px;
      }
    }
  }
}
</style>
