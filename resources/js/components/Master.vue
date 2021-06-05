<template>
    <div id="master">
        <div id="header">
            <span id="logo">URAHKUNTS-CRYPTO</span>
            <ul v-if="authenticated == true" id="navigation">
                <li>
                    <router-link to="/wallets">
                        <i class="fa fa-credit-card-alt" aria-hidden="true"></i>
                        <span>Wallets</span>
                    </router-link>
                </li>
            </ul>
        </div>
        <div id="content">
            <router-view></router-view>
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
            authenticated() {
                return this.$store.getters["user/authenticated"];
            }
        },
        methods: {

        },
        mounted() {

        }
    }
</script>
<style scoped lang="scss">
    @import './resources/sass/variables';

    #master {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;

        background-color: white;
        border: 1px solid rgba(0, 0, 0, 0.1);
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 10;
    }



    #header {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 100%;
        height: 50px;
        box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.1);
        z-index: 100;
    }

    #header > #logo {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 300px;
        height: 50px;
        line-height: 50px;

        font-size: 24px;
        font-weight: bold;
        padding: 0px 15px;
    }

    #header > #navigation {
        position: relative;
        float: left;
        width: 100%;
        height: 50px;
        padding-left: 300px;
        list-style: none;
    }

    #navigation > li {
        position: relative;
        float: right;
        width: auto;
        height: 50px;
    }

    #navigation > li > a {
        position: relative;
        float: right;
        width: auto;
        height: 50px;
        line-height: 50px;
        padding: 0px 25px;

        color: black;
        font-weight: bold;
        text-decoration: none;
        transition: background-color 0.2s linear;
    }

    #navigation > li > a:hover {
        background-color: rgb(240, 240, 240);
    }

    #navigation > li > a > i {
        margin-right: 5px;
    }


    #content {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
        padding-top: 50px;
        display: flex;
        z-index: 10;
    }
</style>