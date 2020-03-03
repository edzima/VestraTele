<template>
    <div v-if="isVisible" :style="{top: coords.y+'px',left: coords.x+'px'}" class="tooltip">
      <div v-if='isAllContentVisible' class="row">
        <h5>
          tytuł:
        </h5>
        <p>
          {{calendarEvent.title}}
        </p>
      </div>
      <div v-if='isAllContentVisible' class="row">
        <h5>
          klient:
        </h5>
        <p>
          {{calendarEvent.extendedProps.client}}
        </p>
      </div>
      <div v-if='calendarEvent.extendedProps.phone' class="row">
        <h5>
          tel:
        </h5>
        <p>
          {{calendarEvent.extendedProps.phone}}
        </p>
      </div>
      <div v-if='calendarEvent.extendedProps.city' class="row">
        <h5>
          miasto:
        </h5>
        <p>
          {{calendarEvent.extendedProps.city}}
        </p>
      </div>
      <div v-if='calendarEvent.extendedProps.city' class="row">
        <h5>
          adres:
        </h5>
        <p>
          {{calendarEvent.extendedProps.address}}
        </p>
      </div>
      <div v-if="isAllContentVisible" class="time">
        {{eventDateRange}}
      </div>
    </div>
</template>

<script lang="ts">
import { Vue, Prop, Component, Watch } from 'vue-property-decorator'
import { prettifyHourRange } from '@/helpers/dateHelper.ts'
@Component({})
export default class ToolTip extends Vue {
  @Prop({
    required: true
  })
  private isVisible!: boolean

  @Prop({
    required: true
  })
  private calendarEvent!: any

  @Prop({
    required: true
  })
  private activeView!: string

  @Prop({
    required: true
  })
  private element!: any

  @Prop({})
  private isAllContentVisible!: boolean

  private coords: any = { x: 0, y: 0 }

  private offset = {
    y: 0,
    x: 10 // offset in pixels
  }

  @Watch('isVisible')
  onPropertyChanged (value: boolean) {
    // cancel if mouseOut
    if (!value) return
    if (this.isAllContentVisible) {
      this.coords = {
        x: 0,
        y: screen.height / 4
      }
      return
    }

    // get the position of the hover element
    const boundBox = this.element.getBoundingClientRect()
    const coordX = boundBox.left
    const coordY = boundBox.top

    if (this.activeView === 'dayGridDay') {
      this.coords = {
        x: coordX + screen.width / 2.2,
        y: coordY + this.offset.y - 80
      }
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

  get eventDateRange () {
    return prettifyHourRange(this.calendarEvent.start, this.calendarEvent.end)
  }
}
</script>

<style lang='less'>
.tooltip {
  position: fixed;
  height:auto;
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
  .time{
    text-align: center;
    font-size: 25px;
  }
}
@media screen and (max-width: 600px) {
  .tooltip{
      position: fixed;
      height: 50vh;
      padding: 10px;
      width: 95vw;
      font-size: 20px;
  }
}
</style>
