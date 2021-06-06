<template>
    <div id="wallets">
        <div id="wallet-list">

        </div>
        <div id="wallet-content">

        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {

            };
        },
        computed: {
            
        },
        methods: {
            
        },
        mounted() {

        }
    }
</script>
<style scoped lang="scss">
    @import './resources/sass/variables';

    #wallets {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
    }


    #wallet-list {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 200px;
        height: 100%;
        z-index: 10;
    }

    #wallet-content {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
        padding-left: 200px;
        z-index: 1;
    }




</style>