import { mapGetters } from 'vuex';

import stringIsNumeric from './stringIsNumericMixin';

const switchTitleMixin = {

  mixins: [stringIsNumeric],

  computed: {
    ...mapGetters([
      'groupById'
    ])
  },

  methods: {
    getTitle(target) {
      if (this.stringIsNumeric(target) && this.groupById(target)) {
        return 'Group ' + this.groupById(target).name;
      }

      if (target === 'last-sixteen') {
        return 'Last Sixteen';
      }

      if (target === 'quarter-final') {
        return 'Quarter Final';
      }

      if (target === 'semi-final') {
        return 'Semi Final';
      }

      if (target === '3rd-place') {
        return '3rd Place';
      }

      if (target === 'final') {
        return 'Final';
      }

      return false;
    }
  }
};

export default switchTitleMixin;
