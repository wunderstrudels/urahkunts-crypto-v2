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
				axios.get(window.api + '/graph/' + data.short + "?from=" + data.from + "&to=" + data.to).then(response => {
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