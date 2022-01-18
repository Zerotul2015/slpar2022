import axios from "axios";
import store from "../store/index"

export const authApi = {
    check() {
        return axios.post('/admin/api/authorization', {'action': 'checkAuth'})
            .then((r) => {
                    //eventBus.$emit('auth-change', r.data.isAuth, r.data.accessLevel, r.data.tokenAuth)
                    console.log(r.data);
                    store.commit('authChange', r.data);
                    return r.data.isAuth;
            })
            .catch((e) => {
                    return false;
                }
            )
    }
}

export default authApi