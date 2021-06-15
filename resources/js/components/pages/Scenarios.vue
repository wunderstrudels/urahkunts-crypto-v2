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
            <div v-show="tab == 'graph'" id="graph">
                <div id="overlay">
                    <span id="profit">$$$ {{ profits }}</span>
                </div>
                <div ref="chart" id="chart"></div>
            </div>

            <!-- BOTS -->
            <div v-show="tab == 'code'" id="code">
                <div id="code-buy">
                    <textarea v-model="buy"></textarea>
                    <span class="code-bottom">Buy</span>
                </div>
                <div id="code-sell">
                    <textarea v-model="sell"></textarea>
                    <span class="code-bottom">Sell</span>
                </div>
                <span v-on:click="save" id="code-save">Save</span>
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
                active: null,
                profit: 0,
                buy: "",
                sell: ""
            };
        },
        computed: {
            scenarios() {
                return this.$store.getters["scenario/list"];
            },
            profits() {
                return this.profit;
            }
        },
        methods: {
            save() {
                this.$store.dispatch("scenario/code", {
                    id: this.active,
                    buy: this.buy,
                    sell: this.sell
                }).then((response) => {

                    window.location.reload();
                });
            },
            selectTab(tab) {
                this.tab = tab;
            },
            selectScenario(scenario) {
                if(scenario == this.active) {
                    return false;
                }

                this.active = scenario;
                this.$router.push("/scenarios/" + this.active);

                
                this.$store.dispatch("scenario/graph", this.active).then((response) => {

                    this.buy = response.buy;
                    this.sell = response.sell;

                    let annotations = {};
                    annotations.points = [];
                    for(let point in response.graph.points) {
                        let item = response.graph.points[point];
                        if(item.label == "Buy") {
                            annotations.points.push({
                                x: item.buy_time,
                                y: item.buy_value,
                                marker: {
                                    size: 8,
                                    fillColor: '#fff',
                                    strokeColor: item.color,
                                    radius: 2,
                                    cssClass: 'apexcharts-custom-class'
                                },
                                label: {
                                    borderColor: item.color,
                                    offsetY: 0,
                                    style: {
                                        color: '#fff',
                                        background: item.color,
                                    },
                                    text: item.label,
                                }
                            });
                        }else {
                            annotations.points.push({
                                x: item.sell_time,
                                y: item.sell_value,
                                marker: {
                                    size: 8,
                                    fillColor: '#fff',
                                    strokeColor: item.color,
                                    radius: 2,
                                    cssClass: 'apexcharts-custom-class'
                                },
                                label: {
                                    borderColor: item.color,
                                    offsetY: 0,
                                    style: {
                                        color: '#fff',
                                        background: item.color,
                                    },
                                    text: item.label,
                                }
                            });
                        }
                    }

                    // Initial.
                    let options = {
                        series: [
                            {
                                name: "Value",
                                data: response.graph.values
                            }
                        ],
                        chart: {
                            height: this.$refs.chart.clientHeight,
                            type: 'line',
                            id: 'mychart'
                        },
                        annotations: annotations,
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'straight'
                        },
                        grid: {
                            padding: {
                                right: 30,
                                left: 20
                            }
                        },
                        labels: response.graph.labels,
                        xaxis: {
                            type: 'string',
                        },
                        yaxis: {
                            
                        }
                    };
                    
                    let chart = new ApexCharts(this.$refs.chart, options);
                    chart.render();
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