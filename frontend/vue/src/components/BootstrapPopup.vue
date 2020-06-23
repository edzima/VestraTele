<template>
	<div @click="outerClick" class="modal-wrapper" v-if="visible">
		<div @click.stop aria-labelledby="myModalLabel" class="modal show" id="myModal" role="dialog" tabindex="-1">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button @click.prevent="hide" aria-label="Close" class="close" type="button">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title" v-if="title">{{title}}</h4>
					</div>
					<div class="modal-body">
						<slot/>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';

    export interface PopupInterface extends Element {
        readonly show: Function;
        readonly hide: Function;
        readonly visible: boolean;
    }

    @Component({})
    export default class BootstrapPopup extends Vue {

        @Prop() public title!: string;
        @Prop({type: Boolean, default: () => false}) public outerDissmisable!: boolean;

        public visible: boolean = false;

        public show(): void {
            this.visible = true;
        }

        public hide(): void {
            this.visible = false;
        }

        private outerClick(): void {
            if (this.outerDissmisable) {
                this.hide()
            }
        }

    }
</script>
<style lang="less" scoped>
	.modal-wrapper {
		background-color: rgba(0, 0, 0, 0.2);
		position: fixed;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		z-index: 999;

		/deep/ .modal {
			top: 10%;
		}
	}
</style>
