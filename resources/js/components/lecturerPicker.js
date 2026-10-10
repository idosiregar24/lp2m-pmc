import TomSelect from 'tom-select';

/**
 * Searchable single <select> of lecturers (Rekap filter). The options are the
 * server-rendered <option>s, so it still works as a plain select without JS.
 */
export default () => ({
    init() {
        // Tom Select copies the select's classes onto its wrapper; the control has its own styling.
        this.$el.removeAttribute('class');

        new TomSelect(this.$el, {
            allowEmptyOption: true,
            maxOptions: 50,
            placeholder: this.$el.dataset.placeholder,
        });
    },
});
