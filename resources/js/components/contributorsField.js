import TomSelect from 'tom-select';

/**
 * Dynamic contributor rows (peneliti / pengabdi / penulis / pemilik KI).
 * Each row is either an internal lecturer (user_id, picked with Tom Select)
 * or an outside person/institution (name + affiliation). Row order = sort_order.
 */
export default ({ rows = [], lecturers = [], leadLabel = 'Ketua', memberLabel = 'Anggota', memberStart = 1 }) => ({
    rows: [],
    lecturers,
    nextKey: 0,

    init() {
        this.rows = rows.map((row) => this.makeRow(row));
        if (this.rows.length === 0) {
            this.add();
        }
    },

    makeRow(row = {}) {
        const userId = row.user_id ? String(row.user_id) : '';

        return {
            key: this.nextKey++,
            type: userId || !row.name ? 'internal' : 'external',
            user_id: userId,
            name: row.name ?? '',
            affiliation: row.affiliation ?? '',
        };
    },

    add() {
        this.rows.push(this.makeRow());
    },

    remove(index) {
        this.rows.splice(index, 1);
    },

    move(index, direction) {
        const target = index + direction;
        if (target < 0 || target >= this.rows.length) {
            return;
        }
        const [row] = this.rows.splice(index, 1);
        this.rows.splice(target, 0, row);
    },

    label(index) {
        return index === 0 ? leadLabel : `${memberLabel} ${index - 1 + memberStart}`;
    },

    initSelect(el, row) {
        new TomSelect(el, {
            options: this.lecturers,
            items: row.user_id ? [row.user_id] : [],
            valueField: 'id',
            labelField: 'label',
            searchField: ['label'],
            maxOptions: 50,
            placeholder: 'Cari nama atau NIDN/NUPTK dosen…',
            onChange: (value) => {
                row.user_id = value;
            },
        });
    },
});
