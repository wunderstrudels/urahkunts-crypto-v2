<template>
    <div id="graphs">
        <div id="graph-list">
            <ul>
                <li v-on:click="select('value')">
                    <i class="fa fa-money" aria-hidden="true"></i>
                    <span>Value</span>
                </li>
                <li v-on:click="select('high/low')">
                    <i class="fa fa-money" aria-hidden="true"></i>
                    <span>High / low</span>
                </li>
            </ul>
        </div>
        <div ref="content" v-show="active != null" id="graph-content">
            <div v-if="currency == null" class="no-active">Select a currency</div>
            <div ref="chart" id="chart"></div>
        </div>

        <div v-if="active != null" id="currency-dropdown">
            <select id="dropdown" v-model="currency" v-on:change="change">
                <option v-for="currency in currencies" v-bind:value="currency.short">{{ currency.short }}</option>
            </select>
        </div>
        <div v-if="active == null" class="no-active">Select a graph</div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                active: null,
                currency: "BTC",
                from: null,
                to: null
            };
        },
        computed: {
            currencies() {
                return this.$store.getters["currency/list"];
            }
        },
        methods: {
            select(graph) {

                if(this.active == graph) {
                    return false;
                }

                this.active = graph;

                if(this.active == "value") {
                    this.values(this.currency);
                }
            },
            change() {
                if(this.active == null) {
                    return false;
                }

                if(this.active == "value") {
                    this.values(this.currency);
                }
            },




            values(currency) {
                this.$store.dispatch("graph/read", {
                    short: this.currency,
                    from: this.from,
                    to: this.to
                }).then((response) => {
                    let options = {
                        series: [
                            {
                                name: "Value",
                                data: response.graph.values
                            }
                        ],
                        labels: response.graph.labels
                    };
                    ApexCharts.exec('mychart', 'updateOptions', options, false, true);
                });
            }
        },
        mounted() {

            // Currencies
            this.$store.dispatch("currency/list");






            // Graph
            this.from = window.helpers.date(0, -1);
            this.to = window.helpers.date();


            let options = {
                series: [],
                chart: {
                    height: "100%",
                    type: 'line',
                    id: 'mychart'
                },
                annotations: {
                    yaxis: [],
                    xaxis: [],
                    points: []
                },
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
                labels: [],
                xaxis: {
                    type: 'string',
                },
                yaxis: {
                    
                }
            };

            let chart = new ApexCharts(this.$refs.chart, options);
            chart.render();
        }
    }
</script>
<style scoped lang="scss">
    @import './resources/sass/variables';

    #graphs {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
        padding-left: 200px;
    }


    #graph-list {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 200px;
        height: 100%;
        z-index: 10;
        border-right: 1px solid rgb(235, 235, 235);
    }

    #graph-list > ul {
        width: 100%;
        height: auto;
        padding: 0px;
        margin: 0px;
        list-style: none;
    }

    #graph-list li {
        position: relative;
        float: left;
        width: 100%;
        height: 40px;
        padding: 0px 15px;
        cursor: pointer;
        transition: background-color 0.2s linear;
    }

    #graph-list li:hover {
        background-color: rgb(235, 235, 235);
    }

    #graph-list li > i {
        position: absolute;
        width: auto;
        height: 40px;
        line-height: 40px;
    }

    #graph-list li > span {
        position: relative;
        float: left;
        width: 100%;
        height: 40px;
        line-height: 40px;
        
        padding-left: 25px;
        border-bottom: 1px solid rgb(235, 235, 235);
    }

    

    #graph-content {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
        padding-top: 75px;
        z-index: 1;
    }



    #currency-dropdown {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 200px;
        height: 40px;
        z-index: 100;
    }

    #currency-dropdown > select {
        position: relative;
        float: left;
        width: 100%;
        height: 40px;
    }






    #chart {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
    }




    .no-active {
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