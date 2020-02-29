<template>
    <div :style="{top: coords.y+'px',left: coords.x+'px'}" id="tooltip" v-if="isVisible">
      <div class="row">
        <h5>
          tel:
        </h5>
        <p>
          {{calendarEvent.extendedProps.phone || 'brak' }}
        </p>
      </div>
      <div class="row">
        <h5>
          miasto:
        </h5>
        <p>
          {{calendarEvent.extendedProps.city || 'brak' }}
        </p>
      </div>
      <div class="row">
        <h5>
          adres:
        </h5>
        <p>
          {{calendarEvent.extendedProps.address || 'brak' }}
        </p>
      </div>
    </div>
</template>

<script lang="ts">
import { Vue, Prop, Component, Watch } from 'vue-property-decorator'

@Component({})
export default class ToolTip extends Vue {
  @Prop({})
  private isVisible!: boolean

  @Prop({})
  private calendarEvent!: any

  @Prop({})
  private activeView!: string

  @Prop({})
  private element!: any

  private coords: any = { x: 0, y: 0 }

  private offset = {
    y: 0,
    x: 10 // offset in pixels
  }

  @Watch('isVisible')
  onPropertyChanged (value: boolean, oldValue: boolean) {
    // cancel if mouseOut
    if (!value) return

    // get the position of the hover element
    const boundBox = this.element.getBoundingClientRect()
    const coordX = boundBox.left
    const coordY = boundBox.top
    console.log(this.activeView)

    if (this.activeView === 'dayGridDay') {
      this.coords = {
        x: coordX + screen.width / 2.2,
        y: coordY + this.offset.y - 80
      }
      console.log('eee')

      return
    }

    // detemine the half of screed
    const isLeftSide = coordX > screen.width / 2
    if (isLeftSide) {
      this.coords = {
        x: coordX - this.offset.x - 225,
        y: coordY + this.offset.y
      }
    } else {
      this.coords = {
        x: coordX + this.offset.x + this.element.offsetWidth,
        y: coordY + this.offset.y
      }
    }
  }
}
</script>

<style lang='less'>
#tooltip {
  position: fixed;
  height: 20vh;
  background-color: white;
  border: #3788D8 solid 2px;
  z-index: 99;
  pointer-events: none;
  box-shadow: 0 20px 40px rgba(0,0,0,0.5);
  border-radius: 10px;
  width: 180px;
  padding: 20px;
  .row{
    margin: 0;
    width: 100%;
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    align-items: center;
    h5{
      margin: 2vh auto;
    }
    p{
      margin: 0;
      font-size: 25px;
      text-align: right;
      color: #3788D8;
      font-weight: bold;
      width: 100%;;
    }
  }
}
</style>
