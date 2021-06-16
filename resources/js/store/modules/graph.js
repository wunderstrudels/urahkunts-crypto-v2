export default {
	namespaced: true,
	state: {

	},
	getters: {

	},
	mutations: {

	},
	actions: {
        read(context, data) {
			return new Promise((resolve, reject) => {
				let query = "";

				query += (data.id != undefined) ? data.id : "";
				query += (data.from != undefined) ? "?from=" + data.from : "";
				query += (data.to != undefined) ? "&to=" + data.to : "";

				axios.get(window.api + '/graph/' + query).then(response => {
					if(typeof response.data == "undefined") {
						return false;
					}
                    
					resolve(response.data);
				}).catch(error => {
					reject(error);
				});
			});
		},
		transactions(context, data) {
			return new Promise((resolve, reject) => {
				axios.get(window.api + '/graph/transactions/' + data.id).then(response => {
					if(typeof response.data == "undefined") {
						return false;
					}
                    
					resolve(response.data);
				}).catch(error => {
					reject(error);
				});
			});
		},
	}
};