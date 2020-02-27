<template>
  <div v-if="isVisible" class="popup">
    <div class="modal">
      <div class="activeNotes">
        <h1>{{dateFormated}}</h1>
        <h3>aktywne notatki:</h3>
        <div class="notes">
          <div class="note">
            <button>usuń</button>
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Cum accusantium esse libero nihil fuga impedit ex, blanditiis, pariatur vero placeat omnis tenetur, nobis incidunt aliquid magni suscipit iste. Esse, consequuntur!</p>
          </div>
        </div>
      </div>
      <div class="addNote">
      <h3>dodaj notatkę</h3>
      <textarea/>
      <button>Dodaj</button>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { Vue, Prop, Component } from 'vue-property-decorator'

@Component({})
export default class NewNotePopup extends Vue {
  @Prop({
    required: true
  })
  private date!: Date;
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
    width: 50vw;
    min-height: 40vh;
    background-color: white;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    border-radius: 10px;
    padding: 20px;
    border: 4px solid #0058a0;
    h1{
      text-align: center;
    }
    h3{
      text-align: left;
    }
    .activeNotes{
      .notes{
        .note{
          padding: 5px;
          border: solid 2px green;
          border-radius: 5px;
          box-shadow: 0 2px 4px rgba(0,0,0,0.2);
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
        border: solid 2px rgb(0, 102, 255);
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
        background-color: rgb(0, 107, 207);
        font-size: 15px;
        padding: 10px 20px;
      }
    }
  }
}
</style>
