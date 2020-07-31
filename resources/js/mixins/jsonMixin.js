export const jsonMixin = {
  /*
   * concatena 2 json (ex header http)
   */
  concat(o1, o2) {
    for (var key in o2) {
      o1[key] = o2[key];
    }
    return o1;
  }
};
