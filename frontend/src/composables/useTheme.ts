import { ref, watch } from 'vue';

const isDark = ref(false);

export function useTheme() {
  const initTheme = () => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
      isDark.value = savedTheme === 'dark';
    } else {
      isDark.value = true; // default to dark
    }
    applyTheme();
  };

  const toggleTheme = () => {
    isDark.value = !isDark.value;
    applyTheme();
  };

  const applyTheme = () => {
    if (isDark.value) {
      document.documentElement.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    } else {
      document.documentElement.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    }
  };

  return {
    isDark,
    initTheme,
    toggleTheme
  };
}
