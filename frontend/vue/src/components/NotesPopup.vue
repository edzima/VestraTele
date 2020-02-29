<template>
  <div v-if="date" class="popup">
    <div class="modal">
      <button class="close"
      @click="handleClose"
      >X</button>
      <div class="activeNotes">
        <h1>{{dateFormated}}</h1>
        <h3>aktywne notatki:</h3>
        <div class="notes">
          <div v-for="note in dayNotes" :key="note.id" class="note" >
            <button @click="deleteNote(note.id)">usuń</button>
            <p>{{note.title}}</p>
          </div>
        </div>
      </div>
      <div class="addNote">
      <h3>dodaj notatkę</h3>
      <textarea v-model="noteText"/>
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

  private noteText = '';

  private handleClose () {
    this.$emit('close')
  }

  private deleteNote (noteID: number) {
    this.$emit('deleteNote', noteID)
  }

  private addNote () {
    const noteWithoutSpaces = this.noteText.split('').filter(char => char !== ' ')
    if (!noteWithoutSpaces.length) return
    this.$emit('addNote', this.noteText.trim(), this.date)
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
.popup{
  position: absolute;
  width: 100vw;
  height: 100vh;
  z-index: 100;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  .modal{
    position: relative;
    width: 50vw;
    min-height: 40vh;
    background-color: white;
    box-shadow: 0 0 20px rgba(0,0,0,0.6);
    border-radius: 10px;
    padding: 20px;
    border: 4px solid #ffffff59;
    button.close{
      position: absolute;
      top:0;
      right: 0;
      border-radius: 0 10px 0 0 ;
      border: none;
      font-size: 23px;
      color: red;
      cursor: pointer;
    }
    h1{
      text-align: center;
      text-shadow: 4px 2px 10px  rgba(0, 0, 0, 0.2);
      color: #03A9F4;
      font-size: 38px;
    }
    h3{
      text-align: left;
    }
    .activeNotes{
      .notes{
        .note{
          margin: 1vh 0;
          padding: 10px;
          border: solid 1px rgba(165, 165, 165, 0.336);
          border-radius: 5px;
          box-shadow: 0 2px 10px rgba(0,0,0,0.2);
          cursor: pointer;
          position: relative;
          button{
            position: absolute;
            right: 0;
            top: 0;
            left: auto;
            background-color: red;
            border-radius: 0 0 0 10px;
            border: none;
            width: 4.5vw;
            height: 3.5vh;
            color: white;
            cursor: pointer;
            font-size: 13px;
          }
        }
    }}
    .addNote{
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      h3{
        text-align: left;
        align-self: flex-start;
      }
      textarea{
        width: 97%;
        border: solid 2px #03A9F4;
        border-radius: 10px;
        height: 10vh;
        font-size: 17px;
        padding: 10px
      }
      button{
        margin-left: auto;
        margin-top: 10px;
        border-radius: 10px;
        color: white;
        border: none;
        background-color: #03A9F4;
        font-size: 15px;
        padding: 10px 20px;
      }
    }
  }
}
</style>
