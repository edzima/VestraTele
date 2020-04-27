<template>
	<div :style="{top: coords.y+'px',left: coords.x+'px'}" class="calendar-tooltip" v-if="isVisible">
		<template v-if='isAllContentVisible'>
			<div class="time">
				{{eventDateRange}}
			</div>
			<div class="row">
				<h5>
					tytuł:
				</h5>
				<p>
					{{calendarEvent.title}}
				</p>
			</div>
		</template>
		<div class="row" v-if='calendarEvent.extendedProps.phone'>
			<h5>
				tel:
			</h5>
			<p>
				{{calendarEvent.extendedProps.phone}}
			</p>
		</div>
		<div class="row" v-if='calendarEvent.extendedProps.city'>
			<h5>
				miasto:
			</h5>
			<p>
				{{calendarEvent.extendedProps.city}}
			</p>
		</div>
		<div class="row" v-if='calendarEvent.extendedProps.address'>
			<h5>
				adres:
			</h5>
			<p>
				{{calendarEvent.extendedProps.address}}
			</p>
		</div>
	</div>
</template>

<script lang="ts">
    import {Component, Prop, Vue, Watch} from 'vue-property-decorator';
    import {prettifyHourRange} from '@/helpers/dateHelper.ts';

    @Component({})
    export default class ToolTip extends Vue {
        @Prop({
            required: true
        })
        private isVisible!: boolean;

        @Prop({
            required: true
        })
        private calendarEvent!: any;

        @Prop({
            required: true
        })
        private activeView!: string;

        @Prop({
            required: true
        })
        private element!: any;

        @Prop({})
        private isAllContentVisible!: boolean;

        private coords: any = {x: 0, y: 0};

        private offset = {
            y: 0,
            x: 10
        };

        get eventDateRange() {
            return prettifyHourRange(this.calendarEvent.start, this.calendarEvent.end);
        }

        @Watch('isVisible')
        onPropertyChanged(value: boolean) {
            // cancel if mouseOut
            if (!value) return;
            if (this.isAllContentVisible) {
                this.coords = {
                    x: 0,
                    y: screen.height / 4
                };
                return;
            }

            // get the position of the hover element
            const boundBox = this.element.getBoundingClientRect();
            const coordX = boundBox.left;
            const coordY = boundBox.top;

            if (this.activeView === 'dayGridDay') {
                this.coords = {
                    x: coordX + screen.width / 2.2,
                    y: coordY + this.offset.y - 80
                };
                return;
            }

            // detemine the half of screed
            const isRightSide = coordX > window.innerWidth / 2;
            if (isRightSide) {
                this.coords = {
                    x: coordX - this.offset.x - 300,
                    y: coordY + this.offset.y
                };
            } else {
                this.coords = {
                    x: coordX + this.offset.x + this.element.offsetWidth,
                    y: coordY + this.offset.y
                };
            }
        }
    }
</script>

<style lang='less'>
	.calendar-tooltip {
		box-sizing: border-box;
		position: fixed;
		height: auto;
		background-color: white;
		border: #3788D8 solid 2px;
		z-index: 99;
		pointer-events: none;
		box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
		border-radius: 10px;
		width: 300px;
		padding: 20px;

		.row {
			margin: 0;
			width: 100%;
			display: flex;
			flex-direction: row;
			justify-content: flex-start;
			align-items: center;

			h5 {
				font-size: 20px;
				margin: 0.5vh 15px 0.5vh 0;
			}

			p {
				margin: 0;
				font-size: 25px;
				text-align: right;
				color: #3788D8;
				font-weight: bold;
				width: 100%;;
			}
		}

		.time {
			text-align: left;
			font-size: 25px;
			color: green;
		}
	}

	@media screen and (max-width: 600px) {
		.tooltip {
			position: fixed;
			height: 50vh;
			padding: 10px;
			width: 95vw;
			font-size: 20px;
			left: 0;
			right: 0;
		}
	}
</style>
