<template>
    <div id="wallets">
        <div id="wallet-list">
            <ul>
                <li v-for="wallet in wallets" v-bind:key="wallet.id" v-on:click="selectWallet(wallet)">
                    <i class="fa fa-credit-card-alt" aria-hidden="true"></i>
                    <span>{{ wallet.name }}</span>
                </li>
            </ul>
        </div>
        <div v-if="active != null" id="wallet-content">
            <div id="tabs">
                <span v-on:click="selectTab('graph')" v-bind:class="{'active': tab == 'graph'}">Graph</span>
                <span v-on:click="selectTab('bots')" v-bind:class="{'active': tab == 'bots'}">Bots</span>
            </div>

            <!-- GRAPH -->
            <div v-show="tab == 'graph'" class="tab">
                <div id="chart" v-if="render">
                    <chart v-bind:data.sync="chart" />
                </div>
            </div>

            <!-- BOTS -->
            <div v-show="tab == 'bots'" class="tab">
                bots
            </div>
        </div>
        <div v-if="active == null" id="no-active">Select a wallet</div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                tab: "graph",
                active: null,
                wallet: null,
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
            wallets() {
                return this.$store.getters["wallet/list"];
            }
        },
        methods: {
            selectTab(tab) {
                this.tab = tab;
            },
            selectWallet(wallet) {
                this.updated_at = null;

                if(this.active != null && wallet.name == this.active.name) {
                    return false;
                }

                this.active = wallet;
                if(this.$route.params.wallet != this.active.name) {
                    this.$router.push("/wallets/" + wallet.name);
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

                    });

                    
                    await this.$store.dispatch("graph/transactions", {
                        id: this.active.id
                    }).then((response) => {
                        this.chart.overlay.profits = response.transactions.profits;


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
            this.$store.dispatch("wallet/list");

            if(this.$route.params.wallet != undefined) {
                this.$store.dispatch("wallet/read", this.$route.params.wallet).then((response) => {
                    this.selectWallet(response);
                });
            }
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
        padding-left: 200px;
    }


    #wallet-list {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 200px;
        height: 100%;
        z-index: 10;
        border-right: 1px solid rgb(235, 235, 235);
    }

    #wallet-list > ul {
        width: 100%;
        height: auto;
        padding: 0px;
        margin: 0px;
        list-style: none;
    }

    #wallet-list li {
        position: relative;
        float: left;
        width: 100%;
        height: 40px;
        padding: 0px 15px;
        cursor: pointer;
        transition: background-color 0.2s linear;
    }

    #wallet-list li:hover {
        background-color: rgb(235, 235, 235);
    }

    #wallet-list li > i {
        position: absolute;
        width: auto;
        height: 40px;
        line-height: 40px;
    }

    #wallet-list li > span {
        position: relative;
        float: left;
        width: 100%;
        height: 40px;
        line-height: 40px;
        
        padding-left: 25px;
        border-bottom: 1px solid rgb(235, 235, 235);
    }

    

    #wallet-content {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
        padding-top: 50px;
        z-index: 1;
    }

    #wallet-content > #tabs {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 100%;
        height: 40px;
        border-bottom: 1px solid rgb(245, 245, 245);
    }

    #wallet-content > #tabs > span {
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

    #wallet-content > #tabs > span:hover {
        background-color: rgb(245, 245, 245);
    }

    #wallet-content > #tabs > span.active {
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