<template>
    <div class="help help--danger" :id="id">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        name: 'p-help-danger',

        props: {
            text: String,
            id: {
                required: false,
                type: String
            },
        }
    }
</script>