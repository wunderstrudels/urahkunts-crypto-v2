<template>
    <div class="crypto-chart">
        <div ref="chart" id="chart"></div>
    </div>
</template>

<script>
    export default {
        props: {
            data: Object,
        },
        data() {
            return {
                chart: null,
                options: {
                    series: [],
                    chart: {
                        height: "100%",
                        type: 'line',
                        id: 'mychart',
                        animations: {
                            enabled: false
                        }
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
                    labels: [],
                    xaxis: {
                        type: 'string',
                    }
                }
            };
        },
        computed: {

        },
        methods: {

        },
        mounted() {
            this.$set(this.data, 'update', () => {
                this.options.series = (this.data.series != undefined) ? this.data.series : [];
                this.options.labels = (this.data.labels != undefined) ? this.data.labels : [];
                this.options.annotations.points = (this.data.annotations.points != undefined) ? this.data.annotations.points : [];
                console.log("TEST", this.options.annotations.points);
                ApexCharts.exec('mychart', 'updateOptions', this.options, false, true);
            });



            this.chart = new ApexCharts(this.$refs.chart, this.options);
            this.chart.render();
        }
    }
</script>
<style scoped lang="scss">
    @import './resources/sass/variables';

</style>