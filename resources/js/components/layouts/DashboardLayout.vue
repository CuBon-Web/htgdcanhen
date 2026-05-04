<template>
  <div class="wrapper" :class="{ 'nav-open': $sidebar.showSidebar }">
    <a class="menutogle" @click="toggleSidebarMobile">
        <i class="fa fa-bars fa-2x" > </i>
      </a>
    <side-bar :background-color="sidebarBackground">
      <template slot-scope="props" slot="links">
        <user-menu></user-menu>
        <sidebar-item
          :link="{
            name: 'Dashboard',
            icon: 'now-ui-icons design_app',
            path: '/',
          }"
        >
        </sidebar-item>
        <sidebar-item v-for="(i, key) in objSidebar" :key="'side'+key"
          :link="{
            name: i.name,
            icon: 'fab fa-vuejs',
          }"
        >
          <sidebar-item v-for="(item, key) in i.sub" :key="'sub'+key"
            :link="{ name: item.name, path: item.path }"
          ></sidebar-item>
        </sidebar-item>

      </template>
    </side-bar>
    <div class="main-panel">
      <sidebar-share
        :color.sync="sidebarBackground"
        :fixed-navbar.sync="fixedNavbar"
        :sidebarMini.sync="sidebarMini"
        style="cursor: pointer"
      >
      </sidebar-share>
      
      <router-view name="header"></router-view>

      <div
        :class="{ content: !$route.meta.hideContent }"
        @click="toggleSidebar"
      >
        <zoom-center-transition :duration="200" mode="out-in">
          <!-- your content here -->
          <router-view></router-view>
        </zoom-center-transition>
      </div>
      <content-footer v-if="!$route.meta.hideFooter"></content-footer>
    </div>
  </div>
</template>
<script>
import PerfectScrollbar from "perfect-scrollbar";
import "perfect-scrollbar/css/perfect-scrollbar.css";

function hasElement(className) {
  return document.getElementsByClassName(className).length > 0;
}

function initScrollbar(className) {
  if (hasElement(className)) {
    new PerfectScrollbar(`.${className}`);
  } else {
    // try to init it later in case this component is loaded async
    setTimeout(() => {
      initScrollbar(className);
    }, 100);
  }
}

import TopNavbar from "../layouts/dashboard/TopNavbar.vue";
import ContentFooter from "../layouts/dashboard/ContentFooter.vue";
import UserMenu from "../layouts/dashboard/Extra/UserMenu.vue";
import { SlideYDownTransition, ZoomCenterTransition } from "vue2-transitions";
import Vuex from "vuex";
import SidebarShare from "../layouts/dashboard/Extra/SidebarSharePlugin";

export default {
  components: {
    TopNavbar,
    ContentFooter,
    UserMenu,
    ZoomCenterTransition,
    SidebarShare,
  },
  data() {
    return {
      sidebarBackground: "black",
      fixedNavbar: false,
      sidebarMini: false,
      objSidebar: [
      
        {
          icon: "mdi mdi-crosshairs-gps menu-icon",
          name: "Khóa học Online",
          route_name: "",
          sub: [
            {
              name: "Danh sách",
              path: "/product",
            },
            {
              name: "Cấp Học",
              path: "/product/category",
            },
            {
              name: "Lớp Học",
              path: "/product/type",
            }
          ],
        },
        {
          icon: "mdi mdi-crosshairs-gps menu-icon",
          name: "Đề thi",
          route_name: "",
          sub: [
            {
              name: "Danh sách",
              path: "/de-thi/list",
            },
            {
              name: "Khối lớp",
              path: "/quiz/categorymain",
            },
            {
              name: "Môn học",
              path: "/quiz/category",
            },
            {
              name: "Bộ đề",
              path: "/quiz/exam",
            },
          ],
        },
        // {
        //   icon: "mdi mdi-newspaper menu-icon",
        //   name: "Quản lý tài liệu",
        //   route_name: "",
        //   sub: [
        //     {
        //       name: "Danh sách ",
        //       path: "/document",
        //     },
        //     {
        //       name: "Danh mục",
        //       path: "/document/category",
        //     }
        //   ],
        // },
       {
          icon: "mdi mdi-crosshairs-gps menu-icon",
          name: "Đơn hàng đề thi",
          route_name: "",
          sub: [
            {
              name: "Đơn hàng cần kiểm tra",
              path: "/bill/dethi/draft",
            },
            {
              name: "Đơn hàng đã thanh toán",
              path: "/bill/dethi/paymented",
            },
          ],
        },
        {
          icon: "mdi mdi-newspaper menu-icon",
          name: "Đơn hàng khóa học",
          route_name: "",
          sub: [
            {
              name: "Đơn hàng cần kiểm tra",
              path: "/bill/course/draft",
            },
            {
              name: "Đơn hàng đã thanh toán",
              path: "/bill/course/paymented",
            }
          ],
        },
        // {
        //   icon: "mdi mdi-shopping-music menu-icon",
        //   name: "Đơn hàng tài liệu",
        //   route_name: "",
        //   sub: [
        //     {
        //       name: "Đơn hàng cần kiểm tra",
        //       path: "/bill/document/draft",
        //     },
        //     {
        //       name: "Đơn hàng đã thanh toán",
        //       path: "/bill/document/paymented",
        //     },
        //   ],
        // },
        {
          icon: "mdi mdi-newspaper menu-icon",
          name: "Trang nội dung",
          route_name: "",
          sub: [
            {
              name: "Danh sách ",
              path: "/pagecontent",
            }
          ],
        },
        {
          icon: "mdi mdi-newspaper menu-icon",
          name: "Quản lý bài viết",
          route_name: "",
          sub: [
            {
              name: "Danh sách bài viết",
              path: "/blogs",
            },
            {
              name: "Danh mục bài viết",
              path: "/blog/category",
            },
            {
              name: "Loại bài viết",
              path: "/blog/type",
            },
          ],
        },
        {
          icon: "mdi mdi-crosshairs-gps menu-icon",
          name: "Trang chủ",
          route_name: "",
          sub: [
            {
              name: "Điểm khác biệt",
              path: "/promotion",
            },
            {
              name: "Vì sao chọn",
              path: "/bannerads",
            },
          ],
        },
        {
          icon: "mdi mdi-shopping-music menu-icon",
          name: "Tài khoản",
          route_name: "",
          sub: [
            {
              name: "Đổi mật khẩu",
              path: "/changepass",
            }
          ],
        },
        {
          icon: "mdi mdi-newspaper menu-icon",
          name: "Quản lý thành viên",
          route_name: "",
          sub: [
            {
              name: "Danh sách",
              path: "/customer",
            }
          ],
        },
        {
          icon: "mdi mdi-shopping-music menu-icon",
          name: "Quản lý giáo viên",
          route_name: "",
          sub: [
            {
              name: "Danh sách",
              path: "/solution",
            }
          ],
        },
        {
          icon: "mdi mdi-file-image menu-icon",
          name: "Website",
          route_name: "",
          sub: [
            {
              name: "Quản lý banner",
              path: "/banner",
            },
            {
              name: "Quản lý đối tác",
              path: "/partner",
            },
            {
              name: "Cài đặt chung",
              path: "/setting",
            },
          ],
        },
        {
          icon: "mdi mdi-shopping-music menu-icon",
          name: "Quản lý tin nhắn liên hệ",
          route_name: "",
          sub: [
            {
              name: "Danh sách",
              path: "/messcontact",
            }
          ],
        },
        {
          icon: "mdi mdi-newspaper menu-icon",
          name: "Review & Đánh giá",
          route_name: "",
          sub: [
            {
              name: "Video",
              path: "/reviewCus",
            },
            {
              name: "Thành tích",
              path: "/albumAffter",
            },
            {
              name: "Social Network",
              path: "/socicalFeedback",
            }
          ],
        },
      ],
    };
  },
  methods: {
    initScrollbar() {
      let isWindows = navigator.platform.startsWith("Win");
      if (isWindows) {
        initScrollbar("sidenav");
      }
    },
    toggleSidebarMobile(){
      if (this.$sidebar.showSidebar == false) {
        this.$sidebar.displaySidebar(true);
      }else{
        this.$sidebar.displaySidebar(false);
      }
    },
    toggleSidebar() {
      
    },
    minimizeSidebar() {
      if (this.$sidebar) {
        this.$sidebar.toggleMinimize();
        let text = this.$sidebar.isMinimized ? "activated" : "deactivated";
        this.$notify({ type: "info", message: `Sidebar mini ${text}...` });
      }
    },
  },
  mounted() {
    let docClasses = document.body.classList;
    let isWindows = navigator.platform.startsWith("Win");
    if (isWindows) {
      // if we are on windows OS we activate the perfectScrollbar function
      initScrollbar("sidebar");
      initScrollbar("sidebar-wrapper");

      docClasses.add("perfect-scrollbar-on");
    } else {
      docClasses.add("perfect-scrollbar-off");
    }
  },
  computed: {
  },
  watch: {
    sidebarMini() {
      this.minimizeSidebar();
    },
  },
};
</script>
<style lang="scss">
$scaleSize: 0.95;
@keyframes zoomIn95 {
  from {
    opacity: 0;
    transform: scale3d($scaleSize, $scaleSize, $scaleSize);
  }
  to {
    opacity: 1;
  }
}
.main-panel .zoomIn {
  animation-name: zoomIn95;
}
@keyframes zoomOut95 {
  from {
    opacity: 1;
  }
  to {
    opacity: 0;
    transform: scale3d($scaleSize, $scaleSize, $scaleSize);
  }
}
.main-panel .zoomOut {
  animation-name: zoomOut95;
}
</style>
