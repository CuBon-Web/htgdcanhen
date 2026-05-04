// resources/js/child-component.js
import Vue from 'vue';
import NoiDungKhoaHoc from './components/webclient/noidungkhoahoc.vue';
import CauHoiThuongGap from './components/webclient/cauhoithuonggap.vue';
import KhoaHocBaoGom from './components/webclient/khoahocbaogom.vue';
import BaiTest from './components/webclient/baitest.vue';
import tailieu from './components/webclient/tailieu.vue';
import previewuploaddethi from './components/webclient/previewuploaddethi.vue';
import editdethi from './components/webclient/editdethi.vue';
import editquestion from './components/webclient/editquestion.vue';
import CKEditor from 'ckeditor4-vue';

Vue.use(CKEditor);
// import store from './store';
// import router from './router';

import Vuesax from 'vuesax';
import 'vuesax/dist/vuesax.css';
Vue.use(Vuesax);

// Mount từng component nếu element tồn tại
if (document.getElementById('child-component-app')) {
  new Vue({
    el: '#child-component-app',
    //   router,
    //   store,
    components: {
      'noi-dung-khoa-hoc': NoiDungKhoaHoc,
    }
  });
}
if (document.getElementById('cau-hoi-thuong-gap')) {
  new Vue({
    el: '#cau-hoi-thuong-gap',
    //   router,
    //   store,
    components: {
      'cau-hoi-thuong-gap': CauHoiThuongGap,
    }
  });
}
if (document.getElementById('khoa-hoc-bao-gom')) {
  new Vue({
    el: '#khoa-hoc-bao-gom',
    //   router,
    //   store,
    components: {
      'khoa-hoc-bao-gom': KhoaHocBaoGom,
    }
  });
}
if (document.getElementById('bai-test')) {
  new Vue({
    el: '#bai-test',
    //   router,
    //   store,
    components: {
      'bai-test': BaiTest,
    }
  });
}
if (document.getElementById('tai-lieu')) {
  new Vue({
    el: '#tai-lieu',
    //   router,
    //   store,
    components: {
      'tai-lieu': tailieu,
    }
  });
}
if (document.getElementById('preview-file-upload')) {
  new Vue({
    el: '#preview-file-upload',
    //   router,
    //   store,
    components: {
      'preview-file-upload': previewuploaddethi,
    }
  });
}
if (document.getElementById('edit-dethi')) {
  new Vue({
    el: '#edit-dethi',
    //   router,
    //   store,
    components: {
      'edit-dethi': editdethi,
    }
  });
}
if (document.getElementById('edit-question')) {
  new Vue({
    el: '#edit-question',
    //   router,
    //   store,
    components: {
      'edit-question': editquestion,
    }
  });
}