export default {
	namespaced: true,
	state: {
        list: []
	},
	getters: {
        list(state) {
            return state.list;
        }
	},
	mutations: {
        list(state, data) {
            state.list = data;
        }
	},
	actions: {
        list(context, data) {
			return new Promise((resolve, reject) => {
				axios.get(window.api + '/scenarios').then(response => {
					if(typeof response.data == "undefined") {
						return false;
					}
                    
                    context.commit("list", response.data);
					resolve(response.data);
				}).catch(error => {
					reject(error);
				});
			});
		},
        read(context, data) {
			return new Promise((resolve, reject) => {
				axios.get(window.api + '/scenarios/' + data).then(response => {
					if(typeof response.data == "undefined") {
						return false;
					}
                    
					resolve(response.data);
				}).catch(error => {
					reject(error);
				});
			});
		},
        graph(context, data) {
			return new Promise((resolve, reject) => {
				axios.get(window.api + '/scenarios/' + data + "/graph").then(response => {
					if(typeof response.data == "undefined") {
						return false;
					}
                    
					resolve(response.data);
				}).catch(error => {
					reject(error);
				});
			});
		},
        code(context, data) {
			return new Promise((resolve, reject) => {
				axios.post(window.api + '/scenarios/' + data.id + "/code", {buy: data.buy, sell: data.sell}).then(response => {
					if(typeof response.data == "undefined") {
						return false;
					}
                    
					resolve(response.data);
				}).catch(error => {
					reject(error);
				});
			});
		}
	}
};