/**
 * Faculty → study program select pair: the prodi list is filtered by the
 * chosen faculty. Options come from server props (no AJAX).
 */
export default ({ faculty = '', studyProgram = '', programs = [] }) => ({
    faculty: faculty ? String(faculty) : '',
    studyProgram: studyProgram ? String(studyProgram) : '',
    programs,

    get filteredPrograms() {
        return this.programs.filter((program) => String(program.faculty_id) === this.faculty);
    },

    init() {
        this.$watch('faculty', () => {
            if (!this.filteredPrograms.some((program) => String(program.id) === this.studyProgram)) {
                this.studyProgram = '';
            }
        });
    },
});
