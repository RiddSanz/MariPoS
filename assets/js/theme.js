/*!
 * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
 * Copyright 2011-2024 The Bootstrap Authors
 * Licensed under the Creative Commons Attribution 3.0 Unported License.
 */

(() => {
  'use strict'

  const getStoredTheme = () => localStorage.getItem('theme')
  const setStoredTheme = theme => localStorage.setItem('theme', theme)

  const getPreferredTheme = () => {
    const storedTheme = getStoredTheme()
    if (storedTheme) {
      return storedTheme
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
  }

  const updateThemeIcon = (theme) => {
    const themeIcons = document.querySelectorAll('.theme-icon-active')
    if (!themeIcons || themeIcons.length === 0) return

    themeIcons.forEach(themeIcon => {
      // Remove all possible theme classes
      themeIcon.classList.remove('bi-sun-fill', 'bi-moon-stars-fill', 'bi-circle-half')

      // Add the appropriate icon class based on current theme
      if (theme === 'dark' || (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        themeIcon.classList.add('bi-moon-stars-fill')
      } else if (theme === 'light' || (theme === 'auto' && !window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        themeIcon.classList.add('bi-sun-fill')
      } else {
        themeIcon.classList.add('bi-circle-half')
      }
    })
  }

  const setTheme = theme => {
    if (theme === 'auto') {
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
      document.documentElement.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light')
      updateThemeIcon('auto')
    } else {
      document.documentElement.setAttribute('data-bs-theme', theme)
      updateThemeIcon(theme)
    }
  }

  setTheme(getPreferredTheme())

  const showActiveTheme = (theme, focus = false) => {
    // Update every theme dropdown's active state and checkmark
    const allThemeButtons = document.querySelectorAll('[data-bs-theme-value]')
    allThemeButtons.forEach(element => {
      element.classList.remove('active')
      element.setAttribute('aria-pressed', 'false')
      const chk = element.querySelector('.bi-check2')
      if (chk) chk.classList.add('d-none')
    })

    const matches = document.querySelectorAll(`[data-bs-theme-value="${theme}"]`)
    matches.forEach(btnToActive => {
      btnToActive.classList.add('active')
      btnToActive.setAttribute('aria-pressed', 'true')
      const checkIcon = btnToActive.querySelector('.bi-check2')
      if (checkIcon) checkIcon.classList.remove('d-none')
    })

    // Update aria labels for each theme switcher instance
    const switchers = document.querySelectorAll('.bd-theme, #bd-theme')
    switchers.forEach(s => {
      const textEl = s.querySelector('.bd-theme-text') || document.querySelector('#bd-theme-text')
      if (textEl) {
        const label = `${textEl.textContent} (${theme})`
        try { s.setAttribute('aria-label', label) } catch(e) {}
      }
    })

    // Update the theme icon on all switchers
    updateThemeIcon(theme)

    if (focus && switchers.length) {
      try { switchers[0].focus() } catch(e) {}
    }
  }

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    const storedTheme = getStoredTheme()
    if (storedTheme !== 'light' && storedTheme !== 'dark') {
      setTheme(getPreferredTheme())
    }
  })

  function initTheme() {
    showActiveTheme(getPreferredTheme())
    document.querySelectorAll('[data-bs-theme-value]')
      .forEach(toggle => {
        toggle.addEventListener('click', () => {
          const theme = toggle.getAttribute('data-bs-theme-value')
          setStoredTheme(theme)
          setTheme(theme)
          showActiveTheme(theme, true)
        })
      })
  }

  if (document.readyState === 'loading') {
    window.addEventListener('DOMContentLoaded', initTheme)
  } else {
    initTheme()
  }
})()