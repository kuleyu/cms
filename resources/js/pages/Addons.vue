<template>
    <div>
        <portal to="title">
            <app-title icon="box-open">Addons</app-title>
        </portal>

        <portal to="actions">
            <p-button v-modal:upload-addon class="button">Upload Addon</p-button>
        </portal>

        <div class="row">
            <div class="content-container">
                <p-table :endpoint="endpoint" id="addons" sort-by="name" primary-key="handle" key="addons_table">
                    <template slot="name" slot-scope="table">
                        <div class="flex items-center">
                            <p-status
                                v-if="table.record.installed"
                                :value="table.record.enabled"
                                class="mr-2">
                            </p-status>

                            <p-status
                                v-else
                                class="mr-2">
                            </p-status>

                            {{ table.record.name }}
                        </div>
                    </template>

                    <template slot="version" slot-scope="table">
                        <span class="badge">{{ table.record.version }}</span>
                    </template>

                    <template slot="description" slot-scope="table">
                        <span class="text-gray-800 text-sm">{{ table.record.description }}</span>
                    </template>

                    <template slot="actions" slot-scope="table">
                        <p-actions v-show="table.record.installed" :id="'addon_' + table.record.slug + '_actions_installed'" :key="'addon_' + table.record.slug + '_actions_installed'">
                            <p-dropdown-link v-if="table.record.enabled" @click="disable(table.record.slug)">Disable</p-dropdown-link>
                            <p-dropdown-link v-else @click="enable(table.record.slug)">Enable</p-dropdown-link>

                            <p-dropdown-link @click.prevent v-modal:uninstall-addon="table.record.slug" classes="link--danger">
                                Uninstall
                            </p-dropdown-link>
                        </p-actions>

                        <p-actions v-show="!table.record.installed" :id="'addon_' + table.record.slug + '_actions_uninstalled'" :key="'addon_' + table.record.slug + '_actions_uninstalled'">
                            <p-dropdown-link v-modal:install-addon="table.record.slug">
                                Install
                            </p-dropdown-link>
                        </p-actions>
                    </template>
                </p-table>
            </div>
        </div>

        <portal to="modals">
            <p-modal name="uninstall-addon" title="Uninstall Addon" key="uninstall_addon">
                <p>Existing data related to this addon will be removed.</p>
                <p>Are you sure you want to uninstall this addon?</p>

                <template slot="footer" slot-scope="addon">
                    <p-button v-modal:uninstall-addon @click="uninstall(addon.data)" theme="danger" class="ml-3">Uninstall</p-button>
                    <p-button v-modal:uninstall-addon>Cancel</p-button>
                </template>
            </p-modal>

            <p-modal name="install-addon" title="Install Addon" key="install_addon">
                <p>Are you sure you want to install this addon?</p>

                <template slot="footer" slot-scope="addon">
                    <p-button v-modal:install-addon @click="install(addon.data)" theme="success" class="ml-3">Install</p-button>
                    <p-button v-modal:install-addon>Cancel</p-button>
                </template>
            </p-modal>

            <p-modal name="update-addon" title="Update Module" key="update_addon">
                <p>This will migrate any new migrations and run db:seed.</p>
                <p>Are you sure you want to proceed?</p>

                <template slot="footer" slot-scope="addon">
                    <p-button v-modal:update-addon @click="update(addon.data)" theme="warning" class="ml-3">Update</p-button>
                    <p-button v-modal:update-addon>Cancel</p-button>
                </template>
            </p-modal>

            <p-modal name="upload-addon" title="Upload Addon" key="upload_addon">
                <p-upload
                    name="file-upload"
                    ref="upload"
                    accept="zip"
                    :multiple="false"
                    @input="upload"
                ></p-upload>
            </p-modal>
        </portal>
    </div>
</template>

<script>
    export default {
        head: {
            title() {
                return {
                    inner: 'Addons'
                }
            }
        },

        data() {
            return {
                endpoint: '/datatable/addons',
            }
        },

        methods: {
            upload(files) {
                if (typeof files == 'undefined') {
                    return;
                }

                const formData = new FormData()

                formData.append('_method', 'POST')
                formData.append('file-upload', files)

                axios.post('/api/addons/upload', formData)
                    .then((response) => {
                        bus().$emit('toggle-modal-upload-addon')
                        this.$refs.upload.remove()
                        this.refresh('Addon successfully uploaded.')
                    })
                    .catch((error) => this.refresh(error.response.data.message, 'danger'))
            },

            enable(slug) {
                axios.post(`/api/addons/${slug}/enable`)
                    .then((response) => {
                        this.refresh('Addon successfully enabled.')

                        if (response.data.data.redirect && response.data.data.redirect.enable) {
                            this.$router.push(response.data.data.redirect.enable)
                        }
                    })
                    .catch((error) => this.refresh(error.response.data.message, 'danger'))
            },

            disable(slug) {
                axios.post(`/api/addons/${slug}/disable`)
                    .then((response) => this.refresh('Addon successfully disabled.'))
                    .catch((error)   => this.refresh(error.response.data.message, 'danger'))
            },

            install (slug) {
                axios.post(`/api/addons/${slug}/install`)
                    .then((response) => {
                        this.refresh('Addon successfully installed.')

                        if (response.data.data.redirect.install) {
                            location.href = `/${config.path}/${response.data.data.redirect.install}`
                        }
                    })
                    .catch((error) => this.refresh(error.response.data.message, 'danger'))
            },

            uninstall(slug) {
                axios.post(`/api/addons/${slug}/uninstall`)
                    .then((response) => this.refresh('Addon successfully uninstalled.'))
                    .catch((error)   => this.refresh(error.response.data.message, 'danger'))
            },

            refresh(msg = null, status = 'success') {
                if (msg) toast(msg, status)

                this.$store.dispatch('navigation/fetchAdminNavigation')
                bus().$emit('refresh-datatable-addons')
            }
        }
    }
</script>
