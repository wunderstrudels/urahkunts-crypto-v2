<template>
    <div id="trainer">
        <div id="trainer-left">
            <div class="container">
                <div class="row" style="margin-top: 25px;">
                    <div class="col-12">
                        <span id="heading"><strong>Wallet:</strong> {{ title }}</span>
                    </div>
                </div>
                <div class="row" style="margin-top: 25px;">
                    <div class="col-12">
                        <p style="font-size: 75px;"><strong>$$$:</strong> <span id="profit" style="text-decoration: underline;">{{ profit }}</span></p>
                    </div>
                </div>
                <div class="row" style="margin-top: 25px;">
                    <div class="col-12">
                        <div v-for="scenario in list" :key="scenario.id" class="scenario">
                            <span v-bind:style="'font-weight: bold; color: ' + scenario.color">Scenario: {{ scenario.name }}</span>
                            <span>Status: {{ scenario.status }}</span>
                            <span>Profit: {{ scenario.profit }}$</span>
                            <span>Success rate: {{ scenario.success }}%</span>
                            <span><strong>Transactions: </strong></span>
                            <ul class="transactions-list">
                                <li v-for="transaction in scenario.transactions" :key="transaction.id">
                                    <p>{{ transaction }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="trainer-right">
            <div ref="chart" id="chart"></div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                wallet: {
                    success: null,
                    profit: null
                },
                scenarios: []
            };
        },
        computed: {
            title() {
                return this.$route.params.wallet;
            },
            profit() {
                return (this.wallet.profit != null) ? this.wallet.profit : "";
            },
            list() {
                return (this.scenarios != null) ? this.scenarios : [];
            }
        },  
        methods: {

        },
        mounted() {
            // Get wallet data.
            this.$store.dispatch("graph/trainer", this.$route.params.scenario).then((response) => {
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

                var options = {
                        series: [
                            {
                                name: "Value",
                                data: response.graph.values
                            }
                        ],
                        labels: response.graph.labels,
                        annotations
                    };
                    
                    ApexCharts.exec('mychart', 'updateOptions', options, false, true);
            });











            // Initial.
            let options = {
                series: [],
                chart: {
                    height: this.$refs.chart.clientHeight,
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
    #trainer {
        position: relative;
        float: left;
        width: 100%;
        height: auto;
        min-height: 100%;
        display: flex;
    }
    #trainer * {
        color: rgb(75, 75, 75);
    }
    #trainer-left {
        min-width: 400px;
        max-width: 400px;
        flex: 1;
        overflow: hidden;
        overflow-y: auto;
    }
    #trainer-left p {
        font-size: 12px;
    }
    #heading {
        position: relative;
        float: left;
        width: 100%;
        height: 30px;
        line-height: 30px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
    .scenario {
        position: relative;
        float: left;
        width: 100%;
        height: auto;
        margin: 5px 0px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
    .scenario > span {
        position: relative;
        float: left;
        width: 100%;
        height: auto;
    }
    .scenario > span:nth-child(1) {
        text-decoration: underline;
    }
    .scenario > ul {
        position: relative;
        float: left;
        width: 100%;
        height: auto;
        margin: 0px;
        padding: 0px 15px;
        padding-bottom: 15px;
    }
    /* .scenario > .transactions-list {
        position: relative;
        float: left;
        width: 100%;
        height: 30px;
        line-height: 30px;
        font-weight: bold;
    } */
    #trainer-left ul li p {
        margin: 0px;
        padding: 0px;
    }
    
    #trainer-right {
        flex: 1;
    }
    #trainer-right > #chart {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
    }
</style>