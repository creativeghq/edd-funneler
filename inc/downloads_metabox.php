<custom-edd-funneler-metabox inline-template>
<div>	
<div class="pretty p-switch">
	       <input type="checkbox" v-model="enable_funneler" value="enable" name="custom_edd_funneler[status]" v-on:click="funneler_enable_handler"/>
	       <div class="state">
	           <label>ENABLE FUNNEL</label>
	       </div>
	   </div>	
<div v-if="enable_funneler" class="funneler_add_option">
	<label>
		Add New Funnel
	</label>
	<select v-model="funnel_option" style="width:50%;">
		<option value="" selected="selected">select</option>
		<option v-for="(i, index) in available_funnel_option" :key="index">{{i}}</option>
	</select>


	<input type="button" name="" value="ADD" v-on:click="add_new_funnel_option" class="button button-primary button-large" style="float: right;">
</div>
<div v-if="enable_funneler" v-for="(fun,index1) in selected_funnels" v-bind:key="index1" class="funneler_selections">
	
	<div class="select_page" v-if="typeof fun.page != 'undefined'">
		<div class="step">{{index1+1}}</div>
		<div class="selection_option">
			
			<span class="selected_desc">Selected Page</span>
			<select v-model="selected_funnels[index1].page" :name="'custom_edd_funneler['+index1+'][page]'">
				<option value="">select</option>
				<option v-for="(i,index) in pages" v-bind:key="index" :value="i.ID">{{i.post_title}}</option>
			</select>
			<input type="button" name="" v-on:click="removeFunnel(index1)" value="X" class="remove_btn">
		</div>
	</div>

	<div class="select_bump_item" v-if="typeof fun.bump != 'undefined'">
		<div class="step">{{index1+1}}</div>
		<div class="selection_option">
			
			<span class="selected_desc">Selected Bump</span>
			<select v-model="selected_funnels[index1].bump" :name="'custom_edd_funneler['+index1+'][bump]'">
				<option value="">select</option>
				<option v-for="(i,index) in downloads" v-bind:key="index" :value="i.ID">{{i.post_title}}</option>
			</select>
			<input type="button" name="" v-on:click="removeFunnel(index1)" value="X" class="remove_btn">
		</div>
	</div>

	<div class="select_upsell" v-if="typeof fun.upsells != 'undefined'">
		<div class="step">{{index1+1}}</div>
		<div class="selection_option">
			<span class="selected_desc">Selected Upsell</span>
			<select multiple="true" v-model="selected_funnels[index1].upsells" :name="'custom_edd_funneler['+index1+'][upsells][]'">
				<option v-for="(i,index) in downloads" v-bind:key="index" :value="i.ID">{{i.post_title}}</option>
			</select>
			<input type="button" name="" v-on:click="removeFunnel(index1)" value="X" class="remove_btn">
		</div>
	</div>

	<div class="select_modal" v-if="typeof fun.modal != 'undefined'">
		<div class="step">{{index1+1}}</div>
		<div class="selection_option">
			<span class="selected_desc">Selected Modal</span>
			<textarea v-model="selected_funnels[index1].modal" :name="'custom_edd_funneler['+index1+'][modal]'" rows="5" cols="40"></textarea>
			<input type="button" name="" v-on:click="removeFunnel(index1)" value="X" class="remove_btn">
		</div>
		
	</div>
	
</div>


</div>

</custom-edd-funneler-metabox>

