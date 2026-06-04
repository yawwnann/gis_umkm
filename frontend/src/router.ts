import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';
import MapView from './pages/map/MapView.vue';
import Login from './pages/auth/Login.vue';
import UmkmDetail from './pages/umkm/UmkmDetail.vue';

// Dashboard / Admin components
import DashboardLayout from './layouts/DashboardLayout.vue';
import DashboardIndex from './pages/dashboard/DashboardIndex.vue';
import ByVillage from './pages/dashboard/ByVillage.vue';
import ByCategory from './pages/dashboard/ByCategory.vue';
import ByPotential from './pages/dashboard/ByPotential.vue';

import UmkmList from './pages/umkm/UmkmList.vue';
import UmkmForm from './pages/umkm/UmkmForm.vue';

import VillageList from './pages/village/VillageList.vue';
import VillageDetail from './pages/village/VillageDetail.vue';

import UserList from './pages/user/UserList.vue';
import NotFound from './pages/NotFound.vue';

const routes: Array<RouteRecordRaw> = [
  // Public Routes
  {
    path: '/',
    name: 'home',
    component: MapView,
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    beforeEnter: (to, from, next) => {
      const token = localStorage.getItem('auth_token');
      const userStr = localStorage.getItem('user_info');
      if (token && userStr) {
        try {
          const user = JSON.parse(userStr);
          if (user.role === 'admin') {
            next({ name: 'dashboard' });
          } else {
            next();
          }
        } catch(e) {
          next();
        }
      } else {
        next();
      }
    }
  },
  {
    path: '/umkm/:id',
    name: 'umkm.show.public',
    component: UmkmDetail,
    props: true,
  },

  // Protected Routes (Admin Only)
  {
    path: '/admin',
    component: DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/admin/dashboard'
      },
      {
        path: 'dashboard',
        name: 'dashboard',
        component: DashboardIndex,
      },
      {
        path: 'dashboard/by-village',
        name: 'dashboard.by-village',
        component: ByVillage,
      },
      {
        path: 'dashboard/by-category',
        name: 'dashboard.by-category',
        component: ByCategory,
      },
      {
        path: 'dashboard/by-potential',
        name: 'dashboard.by-potential',
        component: ByPotential,
      },

      // UMKM CRUD within Dashboard layout
      {
        path: 'umkm',
        name: 'umkm.index',
        component: UmkmList,
      },
      {
        path: 'umkm/create',
        name: 'umkm.create',
        component: UmkmForm,
      },
      {
        path: 'umkm/:id/edit',
        name: 'umkm.edit',
        component: UmkmForm,
        props: true,
      },
      {
        path: 'umkm/:id',
        name: 'umkm.show',
        component: UmkmDetail,
        props: true,
      },
      // Villages within Dashboard layout
      {
        path: 'villages',
        name: 'villages.index',
        component: VillageList,
      },
      {
        path: 'villages/:id',
        name: 'villages.show',
        component: VillageDetail,
        props: true,
      },
      // User management
      {
        path: 'users',
        name: 'users.index',
        component: UserList,
      }
    ]
  },

  // 404 Route
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFound,
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Global Router Guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token');
  const userStr = localStorage.getItem('user_info');
  const user = userStr ? JSON.parse(userStr) : null;
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);

  if (requiresAuth) {
    if (!token || !user) {
      next({ name: 'login' });
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;