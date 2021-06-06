export default {
	namespaced: true,
	state: {

	},
	getters: {

	},
	mutations: {

	},
	actions: {
        trainer(context, data) {
			return new Promise((resolve, reject) => {
				axios.get(window.api + '/graph/trainer/' + data).then(response => {
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