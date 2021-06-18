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
                <chart v-bind:data.sync="graph"/>
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
                graph: {
                    series: [],
                    labels: [],
                    annotations: {
                        points: []
                    }
                },
                timer: null,
                minute: null,
                last: window.helpers.date()
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
                
                if(this.active != null && wallet.name == this.active.name) {
                    return false;
                }

                this.$router.push("/wallets/" + wallet.name);
                this.active = wallet;
                clearInterval(this.timer);

                (async () => {

                    await this.$store.dispatch("graph/read", {
                        id: this.active.currency_id
                    }).then((response) => {
                        this.graph.series = [{
                            name: "Values",
                            data: response.graph.values
                        }];
                        this.graph.labels = response.graph.labels;
                    });




                    await this.$store.dispatch("graph/transactions", {
                        id: this.active.id
                    }).then((response) => {

                        let annotations = {};
                        annotations.points = [];
                        for(let point in response.transactions.points) {
                            let item = response.transactions.points[point];
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

                        this.graph.annotations.points = annotations.points;
                    });

                    this.graph.update();
                })();


                this.timer = (this.timer != null) ? this.timer : setInterval(()=> {
                    let temp = this.last;
                    this.last = window.helpers.date();

                    if(temp == null) {
                        return false;
                    }

                    

                    (async () => {

                        await this.$store.dispatch("graph/read", {
                            id: this.active.currency_id,
                            from: temp
                        }).then((response) => {

                            for(let item in response.graph.values) {
                                this.graph.series[0].data.push(response.graph.values[item]);
                                this.graph.series[0].data.shift();
                            }

                            for(let item in response.graph.labels) {
                                this.graph.labels.push(response.graph.labels[item]);
                                this.graph.labels.shift();
                            }
                        });


                        await this.$store.dispatch("graph/transactions", {
                            id: this.active.id
                        }).then((response) => {

                            let annotations = {};
                            annotations.points = [];
                            for(let point in response.transactions.points) {
                                let item = response.transactions.points[point];
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

                            this.graph.annotations.points = annotations.points;
                        });

                        this.graph.update();
                    })();
                }, 21000);
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