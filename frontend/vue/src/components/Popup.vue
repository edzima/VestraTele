<template>
	<div class="modal-wrapper" v-if="visible">
		<div aria-labelledby="myModalLabel" class="modal show" id="myModal" role="dialog" tabindex="-1">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header" v-if="hasSlot('header')">
						<slot name="header"></slot>
					</div>
					<div class="modal-body" v-if="hasSlot('body')">
						<slot name="body"></slot>
					</div>
					<div class="modal-footer" v-if="hasSlot('footer')">
						<slot name="footer"></slot>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';

    export interface PopupInterface {
        readonly show: Function;
        readonly hide: Function;
        readonly visible: boolean;
    }

    @Component({})
    export default class BootstrapPopup extends Vue {

        public visible: boolean = false;

        public show(): void {
            this.visible = true;
        }

        public hide(): void {
            this.visible = false;
        }

        private hasSlot(slotName: string): boolean {
            return !!this.$slots[slotName];
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
			position: initial;
		}
	}
</style>
