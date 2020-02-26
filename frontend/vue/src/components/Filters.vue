<template>
  <div class="additionalControls">
    <div class="leftControls">
      <button
        :style="buttonStyles[type.id-1]"
        :class="isFilterActive(type.id) ? '' : 'disabled'"
        @click="toggleFilter(type.id)"
        v-for="type in eventTypes"
        :key="type.id"
      >{{type.name}}</button>
    </div>
    <div class="rightControls"></div>
  </div>
</template>

<script lang="ts">
import { Vue, Prop, Component } from 'vue-property-decorator'

@Component({})
export default class Filters extends Vue {
  @Prop({
    required: false
  })
  private activeFilters!: Array<number>;

  @Prop({
    default: () => [],
    required: true
  })
  private eventTypes!: Array<any>;

  private buttonStyles: Array<any> = [
    { backgroundColor: 'blue', color: 'white' },
    { backgroundColor: 'green', color: 'white' },
    { backgroundColor: 'red', color: 'white' },
    { backgroundColor: 'yellow', color: 'black' }
  ]

  private isFilterActive (filterId: number): boolean {
    return this.activeFilters.includes(filterId)
  }

  private toggleFilter (filterId: number): void{
    this.$emit('toggleFilter', filterId)
  }
}
</script>

<style scoped lang='less'>
.additionalControls {
  .leftControls {
    width: 45vw;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    button {
      height: 5vh;
      width: 10vw;
      border-radius: 10px;
      font-size: 15px;
      color: white;
      border: none;
      margin: 0 auto;
      box-shadow: 0 4px 5px rgba(0, 0, 0, 0.1);
      &.disabled {
        // background-color: #fff !important;
        // color: black !important;
        opacity: 0.4;
      }
    }
  }
}
</style>
