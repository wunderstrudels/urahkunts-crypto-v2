<template>
    <div id="scenarios">
        <div id="scenario-list">
            <ul>
                <li v-for="scenario in scenarios" v-bind:key="scenario.id" v-on:click="selectScenario(scenario.name)">
                    <i class="fa fa-flask" aria-hidden="true"></i>
                    <span>{{ scenario.name }}</span>
                </li>
            </ul>
        </div>
        <div v-if="active != null" id="scenario-content">
            <div id="tabs">
                <span v-on:click="selectTab('graph')" v-bind:class="{'active': tab == 'graph'}">Graph</span>
                <span v-on:click="selectTab('code')" v-bind:class="{'active': tab == 'code'}">Code</span>
            </div>

            <!-- GRAPH -->
            <div v-show="tab == 'graph'">
                graph
            </div>

            <!-- BOTS -->
            <div v-show="tab == 'code'">
                code
            </div>
            <div id="training-overlay">Scenario is training.</div>
        </div>
        <div v-if="active == null" id="no-active">Select a scenario</div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                tab: "graph",
                active: null
            };
        },
        computed: {
            scenarios() {
                return this.$store.getters["scenario/list"];
            }
        },
        methods: {
            selectTab(tab) {
                this.tab = tab;
            },
            selectScenario(scenario) {
                this.active = scenario;
            }
        },
        mounted() {
            this.$store.dispatch("scenario/list");

            if(this.$route.params.scenario != undefined) {
                this.select(this.$route.params.scenario);
            }
        }
    }
</script>
<style scoped lang="scss">
    @import './resources/sass/variables';

    #scenarios {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
        padding-left: 200px;
    }


    #scenario-list {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 200px;
        height: 100%;
        z-index: 10;
        border-right: 1px solid rgb(235, 235, 235);
    }

    #scenario-list > ul {
        width: 100%;
        height: auto;
        padding: 0px;
        margin: 0px;
        list-style: none;
    }

    #scenario-list li {
        position: relative;
        float: left;
        width: 100%;
        height: 40px;
        padding: 0px 15px;
        cursor: pointer;
        transition: background-color 0.2s linear;
    }

    #scenario-list li:hover {
        background-color: rgb(235, 235, 235);
    }

    #scenario-list li > i {
        position: absolute;
        width: auto;
        height: 40px;
        line-height: 40px;
    }

    #scenario-list li > span {
        position: relative;
        float: left;
        width: 100%;
        height: 40px;
        line-height: 40px;
        
        padding-left: 25px;
        border-bottom: 1px solid rgb(235, 235, 235);
    }

    

    #scenario-content {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
        padding-top: 50px;
        z-index: 1;
    }

    #scenario-content > #tabs {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 100%;
        height: 40px;
        border-bottom: 1px solid rgb(245, 245, 245);
    }

    #scenario-content > #tabs > span {
        position: relative;
        float: left;
        width: auto;
        min-width: 150px;
        height: 40px;
        line-height: 40px;
        text-align: center;


        padding: 0px 15;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.2s linear;
    }

    #scenario-content > #tabs > span:hover {
        background-color: rgb(245, 245, 245);
    }

    #scenario-content > #tabs > span.active {
        background-color: rgb(245, 245, 245);
    }







    #no-active {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
        line-height: 70vh;

        font-size: 50px;
        color: rgb(210, 210, 210);
        text-align: center;
    }
</style>