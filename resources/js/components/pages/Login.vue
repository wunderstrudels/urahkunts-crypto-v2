<template>
    <div id="login">
        <form v-on:submit.prevent="submit">
            <input v-model="username" type="text" placeholder=" Username:">
            <input v-model="password" type="password" placeholder=" Password:">
            <input type="submit" value="Signin">
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                username: '',
                password: ''
            };
        },
        computed: {

        },
        methods: {
            submit() {
                console.log("lulz");
                this.$store.dispatch("user/login", {
                    'username': this.username,
                    'password': this.password
                }).then((response) => {
                    this.$router.push("/");
                }).catch((error) => {
                    alert("Login error.");
                });
            }
        },
        mounted() {

        }
    }
</script>
<style scoped lang="scss">
    @import './resources/sass/variables';

    #login {
        position: relative;
        float: left;
        width: 100%;
        height: 100%;

        background-image: url("https://media.discordapp.net/attachments/403651330901803008/848386384527622144/ZNFMTWerFYngqFZatJG9MM-1200-80.png?width=939&height=521");
        background-size: 100% 100%;
        background-position: bottom center;
    }

    form {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 400px;
        height: auto;

        transform: translate(-50%, -50%);
    }

     form > input {
         width: 100%;
         height: 40px;
         margin: 10px 0px;
         border: 1px solid rgb(150, 150, 150);
         outline: none;
     }
</style>