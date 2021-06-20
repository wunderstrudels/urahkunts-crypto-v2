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
            </div>

            <!-- GRAPH -->
            <div v-show="tab == 'graph'" id="graph">
                <div id="chart" v-if="render">
                    <chart v-bind:data.sync="chart" />
                </div>
            </div>
        </div>
        <div v-if="active == null" id="no-active">Select a scenario</div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                tab: "graph",
                active: null,
                render: false,
                chart: {
                    values: [],
                    labels: [],
                    annotations: {},
                    overlay: {
                        profits: 0,
                        bots: []
                    }
                }
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
                if(scenario == this.active) {
                    return false;
                }

                this.render = false;
                this.chart = {
                    values: [],
                    labels: [],
                    annotations: {},
                    overlay: {
                        profits: 0,
                        bots: []
                    }
                };

                this.active = scenario;
                if(this.$route.params.scenario != this.active) {
                    this.$router.push("/scenarios/" + this.active);
                }

                
                this.$store.dispatch("scenario/graph", this.active).then((response) => {

                    this.chart.values = response.graph.values;
                    this.chart.labels = response.graph.labels;





                    for(let item in response.graph.points) {
                            let point = response.graph.points[item];


                            if(point.label == "Buy") {
                                this.chart.annotations["buy-" + item] = {
                                    type: 'point',
                                    xValue: point.buy_time,
                                    yValue: point.buy_value,
                                    backgroundColor: point.color,
                                    borderColor: "black",
                                    borderWidth: 1,
                                    radius: 6
                                };
                            }else {
                                this.chart.annotations["sold-" + item] = {
                                    type: 'point',
                                    xValue: point.sell_time,
                                    yValue: point.sell_value,
                                    backgroundColor: point.color,
                                    borderColor: "black",
                                    borderWidth: 1,
                                    radius: 6
                                };
                                this.chart.annotations["soldd-" + item] = {
                                    type: 'point',
                                    xValue: point.sell_time,
                                    yValue: point.sell_value,
                                    backgroundColor: "black",
                                    borderColor: "black",
                                    borderWidth: 1,
                                    radius: 2
                                };
                            }
                        }


                    
                    this.render = true;
                });
            }
        },
        mounted() {
            this.$store.dispatch("scenario/list");

            

            if(this.$route.params.scenario != undefined) {
                this.selectScenario(this.$route.params.scenario);
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








    #scenario-content > #graph {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
        z-index: 1;
    }


    #graph > #chart {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
    }

    #graph > #overlay {
        position: absolute;
        top: 15px;
        left: 15px;
        width: 300px;
        height: auto;

        padding: 15px;
        background-color: rgba(230, 230, 230, 0.75);
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
        z-index: 100;
    }

    #overlay > #profit {
        position: relative;
        float: left;
        width: 100%;
        height: 50px;
        line-height: 50px;

        font-size: 50px;
        font-weight: bold;
    }



















    #code {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
    }

    #code > div {
        position: relative;
        float: left;
        width: 50%;
        height: 100%;

        padding-bottom: 50px;
    }

    #code > div > textarea {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
        resize: none;
        outline: none;
        border: none;

        padding: 10px;
        border-right: 1px solid rgba(0, 0, 0, 0.1);
    }

    #code > div > .code-bottom {
        position: absolute;
        bottom: 0px;
        left: 0px;
        width: 100%;
        height: 50px;

        text-align: center;
        line-height: 50px;

        font-size: 30px;
        color: rgba(0, 0, 0, 0.3);
    }

    #code > #code-save {
        position: absolute;
        bottom: 10px;
        left: 50%;
        width: 150px;
        height: 30px;
        line-height: 30px;

        cursor: pointer;
        text-align: center;
        padding: 0px 15px;
        background-color: rgb(200, 200, 200);
        border: 1px solid rgb(190, 190, 190);

        transform: translateX(-50%);
        transition: all 0.2s linear;
    }

    #code > #code-save:hover {
        background-color: rgb(220, 220, 220);
        border: 1px solid rgb(210, 210, 210);
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