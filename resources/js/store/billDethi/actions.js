import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


// export const addBill = ({commit},opt) => {
//     return new Promise((resolve, reject) => {
//         HTTP.post('/api/bill/add',opt).then(response => {
//             return resolve(response.data);
//         }).catch(error => {
//             return reject(error);
//         })
//     });
// };
export const listBillDethi = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/dethi/draft',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const detailBillDethi = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/dethi/detail/'+ opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const changeDethiStatus = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/dethi/change-status', opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
