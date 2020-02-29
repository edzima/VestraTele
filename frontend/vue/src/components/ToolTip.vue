<template>
    <div :style="{top: coords.y+'px',left: coords.x+'px'}" id="tooltip" v-if="isVisible">
      <p> tel: <span> {{calendarEvent.extendedProps.phone || 'brak'}} </span></p>
      <p> miasto: <span>{{calendarEvent.extendedProps.city || 'brak' }} </span></p>
      <p> adres:  <span>{{calendarEvent.extendedProps.address || 'brak'}} </span></p>
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

    // detemine the half of screed
    const isLeftSide = coordX > screen.width / 2
    if (isLeftSide) {
      console.log('ee')
    }

    this.coords = {
      x: coordX + this.offset.x + this.element.offsetWidth,
      y: coordY + this.offset.y
    }
  }
}
</script>

<style lang='less'>
#tooltip {
  position: fixed;
  // background-color: #3788D8;
  background-color: white;
  border: #3788D8 solid 2px;
  z-index: 99;
  pointer-events: none;
  box-shadow: 0 20px 40px rgba(0,0,0,0.5);
  border-radius: 10px;
  width: 20vw;
  padding: 20px;
  p{
    width: 18vw;
    span{
      margin-left: auto;
      font-size: 22px;
      color: #3788D8;
      font-weight: bold;
      margin-left: 2em;
      width: 80%;;
    }
  }
}
</style>
