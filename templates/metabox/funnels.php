
<?php $nonce = wp_create_nonce( 'EDD_FUNNELS' ) ?>

<edd-funnels-metabox inline-template nonce="<?php echo esc_attr( $nonce ) ?>">
	<div>
		<input type="hidden" name="edd_funnels_nonce" value="<?php echo esc_attr( $nonce ) ?>">
		<!-- Rounded switch -->
		<div class="input-group mb-20">
			<label><?php esc_html_e( 'Enable Funnels', 'edd-funnels' ) ?></label>
			<div class="input">
				<label class="eddfs-switch">
					<input type="checkbox" v-model="enabled" value="on" name="edd_funnels[status]">
					<span class="eddfs-slider round"></span>
				</label>
			</div>
		</div>
		<transition name="slide-fade">
			<div v-if="enabled">
				<div id="dyanmic-funnels">
					<template v-for="(comp, index) in comps">

						<component :is="comp.is" :title="comp.props.title" :tag="comp.props.tag">
							
							<span @click="remove(index, comp.props.tag)" class="close dashicons dashicons-no-alt"></span>
							<span class="funnels-tag">{{ comp.props.tag }}</span>
							<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
							<input type="hidden" :name="'edd_funnels['+index+'][type]'" :value="comp.props.tag">
							
								<template slot="downloads">
									<label>{{ comp.props.title }}</label>
									<select :name="'edd_funnels['+index+'][object_id]'" required v-model="comps[index].object_id">
										<option value=""><?php esc_html_e('--Select--', 'edd-funnels'); ?></option>
										<option v-for="item in downloads" :value="item.ID">
											{{ item.post_title }}
										</option>
									</select>
								</template>

								<template slot="pages">
									<label>{{ comp.props.title }}</label>
									<select :name="'edd_funnels['+index+'][object_id]'" required v-model="comps[index].object_id">
										<option value=""><?php esc_html_e('--Select--', 'edd-funnels'); ?></option>
										<option v-for="item in pages" :value="item.ID">
											{{ item.post_title }}
										</option>
									</select>
								</template>

								<template slot="multi_downloads">
									<label>{{ comp.props.title }}</label>
									<select multiple :name="'edd_funnels['+index+'][object_id][]'" required v-model="comps[index].object_id" data-placeholder="Choose Items..." class="chosen">
										<option v-for="item in downloads" :value="item.ID">
											{{ item.post_title }}
										</option>
									</select>
								</template>

								<template slot="textarea">
									<label>{{ comp.props.title }}</label>
									<textarea :name="'edd_funnels['+index+'][object_id]'" required v-model="comps[index].object_id" rows="10" cols="60"></textarea>
								</template>

						</component>
					</template>
				</div>
				<!-- Rounded switch -->
				<div class="input-group hiide mt-20">
					<label><?php esc_html_e( 'Add Funnel Section', 'edd-funnels' ) ?></label>
					<div class="input">
						<select name="" id="" v-model="selectedopt">
							<option value=""><?php esc_html_e('--Select--', 'edd-funnels'); ?></option>
							<option value="page"><?php esc_html_e('Page', 'edd-funnels'); ?></option>
							<option value="bump" :disabled="bump_added"><?php esc_html_e('Bump Item', 'edd-funnels'); ?></option>
							<option value="upsells"><?php esc_html_e('Upsells', 'edd-funnels'); ?></option>
							<option value="modal"><?php esc_html_e('Modal', 'edd-funnels'); ?></option>
						</select>
						<button type="button" class="button-primary" @click="add_new()"><?php esc_html_e('Add New', 'edd-funnels'); ?></button>
					</div>
				</div>
			</div>
		</transition>
	</div>
</edd-funnels-metabox>

