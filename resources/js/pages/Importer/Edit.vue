<template>
	<div>
		<portal to="title">
			<app-title icon="ship">Edit Import</app-title>
		</portal>

		<portal to="actions">
			<router-link :to="{ name: 'importer' }" class="button mr-3">Go Back</router-link>
			<button type="submit" @click.prevent="submit" class="button button--primary" :class="{'button--disabled': !form.hasChanges}" :disabled="!form.hasChanges">Save &amp; Continue</button>
		</portal>

		<div class="row">
			<div class="content-container">
				<shared-form :form="form"></shared-form>
			</div>
		</div>
	</div>
</template>

<script>
	import Form from '../../services/Form'
	import SharedForm from './SharedForm.vue'

	export default {
		head: {
            title() {
                return {
                    inner: this.form.name || 'Loading...'
                }
            }
		},

		data() {
			return {
				id: null,
				form: new Form({
					name: '',
					handle: '',
					source: '',
					schedule: null,
					module: 'users',
					group: 0,
					strategy: [],
					backup: false
				}, true)
			}
		},

		components: {
			'shared-form': SharedForm
		},

		methods: {
			submit() {
				this.form.patch(`/api/imports/${this.id}`).then((response) => {
					toast('Import successfully saved', 'success')

					this.$router.push(`/importer/mapping/${response.data.id}`)
				}).catch((response) => {
					toast(response.message, 'failed')
				})
			}
		},

		beforeRouteEnter(to, from, next) {
			axios.all([
				axios.get(`/api/imports/${to.params.importer}`)
			]).then(axios.spread(function (response) {
				next(function (vm) {
					vm.id = response.data.data.id

					_.forEach(response.data.data, function(value, key) {
						if (_.has(vm.form, key)) {
							vm.form[key] = value
						}
					})

					vm.$emit('updateHead')
				})
			}))
		}
	}
</script>