<template>
  <div class="additionalControls">
    <div class="leftControls">
        <h1>Filtry statusów:</h1>
      <div class="buttons">
        <button
          :class="[type.className, isFilterActive(type.id) ? '' : 'disabled']"
          @click="toggleFilter(type.id)"
          v-for="type in eventTypes"
          :key="type.id"
        >{{type.name}}</button>
      </div>
    </div>
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

  private isFilterActive (filterId: number): boolean {
    return this.activeFilters.includes(filterId)
  }

  private toggleFilter (filterId: number): void {
    this.$emit('toggleFilter', filterId)
  }
}
</script>

<style scoped lang='less'>
.additionalControls {
  .leftControls {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 45vw;
    h1{
      margin: 0;
      margin-bottom: 1vh;
      align-self: flex-start;
      font-size: 20px;
    }
    .buttons {
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: center;
      width: 100%;
      margin-bottom: 3vh;
      button {
        height: 5vh;
        width: 10vw;
        border-radius: 10px;
        font-size: 15px;
        color: white;
        border: none;
        margin: 0 auto;
        box-shadow: 0 4px 5px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        &.blue {
          background-color: blue;
        }
        &.green {
          background-color: green;
        }
        &.red {
          background-color: red;
        }
        &.yellow {
          background-color: yellow;
          color: black;
        }
        &.disabled {
          // background-color: #fff !important;
          // color: black !important;
          opacity: 0.4;
        }
      }
    }
  }
}
</style>
