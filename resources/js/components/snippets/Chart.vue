<template>
    <div class="crypto-chart">
        <div class="overlay">
            <span class="profit">$$$ {{ profits }}</span>
            <span class="daily_profit">Today: {{ daily_profit }}$</span>
            <ul v-show="data.overlay.bots.length != 0">
                <li v-for="bot in data.overlay.bots" v-bind:key="bot.name">
                    <strong v-bind:style="'color: ' + bot.color + ';'">{{ bot.name }}:</strong> {{ bot.status }}
                </li>
            </ul>
        </div>
        <canvas ref="chart" class="chart"></canvas>
    </div>
</template>

<script>
    export default {
        props: {
            data: {
                type: Object,
            },
        },
        data() {
            return {
                chart: null
            };
        },
        computed: {
            profits() {
                return (this.data.overlay.profits != undefined) ? this.data.overlay.profits : 0;
            },
            daily_profit() {
                return (this.data.overlay.daily_profit != undefined) ? this.data.overlay.daily_profit : 0;
            }
        },
        methods: {
            
        },
        mounted() {
            
            Vue.set(this.data, "update", () => {
                this.chart.pan({x: -5}, undefined, 'default');
            });

            this.chart = new Chart(this.$refs.chart, {
                type: 'line',
                data:{
                    labels: this.data.labels,
                    datasets: [
                        {
                            label: 'Values',
                            data: this.data.values
                        }
                    ]
                },
                options: {
                    elements: {
                        line: {
                            borderColor: "rgba(82, 157, 232, 0.75)",
                        },
                        point: {
                            backgroundColor: "rgba(0, 0, 0, 0)",
                            borderColor: 'rgba(0, 0, 0, 0)',
                        }
                    },
                    plugins: {
                        zoom: {
                            zoom: {
                                wheel: {
                                    enabled: true,
                                },
                                pinch: {
                                    enabled: true
                                },
                                mode: 'xy',
                            },
                            pan: {
                                enabled: true,
                                mode: "xy"
                            }
                        },
                        annotation: {
                            annotations: this.data.annotations
                        }
                    }
                }
            });
        }
    }
</script>
<style scoped lang="scss">
    @import './resources/sass/variables';

    .crypto-chart, .crypto-chart > canvas {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;
        max-height: 100%;
    }

    .crypto-chart > .overlay {
        position: absolute;
        top: 45px;
        left: 65px;
        width: 300px;
        height: auto;

        padding: 15px;
        background-color: rgba(230, 230, 230, 0.85);
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
        z-index: 100;
    }

    .overlay > .profit {
        position: relative;
        float: left;
        width: 100%;
        height: 50px;
        line-height: 50px;

        font-size: 50px;
        font-weight: bold;
    }

    .overlay > .daily_profit {
        position: relative;
        float: left;
        width: 100%;
        height: 20px;
        line-height: 20px;

        font-size: 20px;
        font-weight: bold;
        margin-top: 10px;
    }

    .overlay > ul {
        position: relative;
        float: left;
        width: 100%;
        height: auto;
        padding: 0px;
        padding-left: 15px;
        margin: 0px;
        margin-top: 15px;
    }

    .overlay > ul > li {
        position: relative;
        float: left;
        width: 100%;
        height: auto;

        font-size: 12px;
    }

</style>