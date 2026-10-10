// Chart colours come from the design tokens (resources/css/app.css), never hex literals.
export const SERIES_TOKENS = ['--color-brand-600', '--color-navy-700', '--color-gold-400', '--color-success', '--color-muted'];

export const cssToken = (name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim();
