<template>
    <div id="public">
        <ul>
            <li v-for="wallet in list" v-bind:key="wallet.id" v-on:click="select(wallet)">
                <i class="fa fa-credit-card-alt" aria-hidden="true"></i>
                <span>{{ wallet.name }}</span>
            </li>
        </ul>
        <div id="chart" v-if="render">
            <chart v-bind:data.sync="chart" />
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                wallets: [],
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
                },
                updated_at: null,
                timer: null
            };
        },
        computed: {
            list() {
                return this.wallets;
            }
        },
        methods: {
            select(wallet) {
                this.updated_at = null;

                if(this.active != null && wallet.name == this.active.name) {
                    return false;
                }

                this.active = wallet;
                if(this.$route.params.wallet != this.active.name) {
                    this.$router.push("/public/" + wallet.name);
                }

                this.chart = {
                    values: [],
                    labels: [],
                    annotations: {},
                    overlay: {
                        profits: 0,
                        bots: []
                    }
                };

                this.graph(this.active.name, true);
                this.timer = (this.timer != null) ? this.timer : setInterval(() => {
                    this.graph(this.active.name, false);
                }, 21000);
            },
            graph(wallet, initial) {
                if(initial == true) {
                    this.render = false;
                }

                let temp = this.updated_at;
                this.updated_at = window.helpers.date();

                let config =  {id: this.active.currency_id};
                if(temp != null) {
                    config.from = temp;
                }

                
                (async () => {
                    await this.$store.dispatch("graph/read", {
                        id: this.active.currency_id,
                        from: temp
                    }).then((response) => {

                        if(this.chart.values.length == 0) {
                            this.chart.values = response.graph.values;
                        }else {
                            for(let item in response.graph.values) {
                                this.chart.values.push(response.graph.values[item]);
                                this.chart.values.shift();
                            }
                        }

                        if(this.chart.labels.length == 0) {
                            this.chart.labels = response.graph.labels;
                        }else {
                            for(let item in response.graph.labels) {
                                this.chart.labels.push(response.graph.labels[item]);
                                this.chart.labels.shift();
                            }
                        }

                        this.chart.annotations["daily-line"] = {
                            type: 'line',
                            yMin: response.graph.daily,
                            yMax: response.graph.daily,
                            borderColor: "rgba(0, 0, 0, 0.2)",
                            borderWidth: 2,
                        };

                        this.chart.annotations["weekly-line"] = {
                            type: 'line',
                            yMin: response.graph.weekly,
                            yMax: response.graph.weekly,
                            borderColor: "rgba(0, 255, 0, 0.2)",
                            borderWidth: 2,
                        };

                        this.chart.annotations["monthly-line"] = {
                            type: 'line',
                            yMin: response.graph.monthly,
                            yMax: response.graph.monthly,
                            borderColor: "rgba(255, 0, 0, 0.2)",
                            borderWidth: 2,
                        };
                    });

                    
                    await this.$store.dispatch("graph/transactions", {
                        id: this.active.id
                    }).then((response) => {
                        this.chart.overlay.profits = response.transactions.profits;
                        this.chart.overlay.daily_profit = response.transactions.daily_profit;


                        for(let item in response.transactions.points) {
                            let point = response.transactions.points[item];


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


                        for(let item in response.transactions.lines) {
                            let line = response.transactions.lines[item];

                            this.chart.annotations["buy-line-" + item] = {
                                type: 'line',
                                yMin: line.buy_value,
                                yMax: line.buy_value,
                                borderColor: line.color,
                                borderWidth: 2,
                            };
                        }
                    });




                    await this.$store.dispatch("graph/bots", {
                        id: this.active.name,
                    }).then((response) => {
                        this.chart.overlay.bots = response;
                    });




                    if(initial == true) {
                        this.render = true;
                    }else {
                        this.chart.update();
                    }
                })();
            }
        },
        mounted() {
            this.$store.dispatch("graph/wallets").then((response) => {
                this.wallets = response.wallets;


                if(this.$route.params.wallet != undefined) {
                    this.wallets.map((item) => {
                        if(item.name.toLowerCase() == this.$route.params.wallet.toLowerCase()) {
                            this.select(item);
                        }
                    });
                }
            });
        }
    }
</script>
<style scoped lang="scss">
    @import './resources/sass/variables';

    #public {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
    }








    ul {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 250px;
        height: 100%;
        padding: 0px;
        margin: 0px;
        list-style: none;
        z-index: 1000;
    }

    ul > li {
        position: relative;
        float: left;
        width: 100%;
        height: 40px;
        padding: 0px 15px;
        cursor: pointer;
        transition: background-color 0.2s linear;
    }

    ul > li:hover {
        background-color: rgb(235, 235, 235);
    }

    ul > li > i {
        position: absolute;
        width: auto;
        height: 40px;
        line-height: 40px;
    }

    ul > li > span {
        position: relative;
        float: left;
        width: 100%;
        height: 40px;
        line-height: 40px;
        
        padding-left: 25px;
        border-bottom: 1px solid rgb(235, 235, 235);
    }











    #public > #chart {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
        
        padding-left: 250px;
        z-index: 10;
    }

</style>