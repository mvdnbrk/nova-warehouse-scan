<script>
export default {
  props: {
    fields: {
      default: [],
    },
    gtin: {
      default: '',
    },
  },

  data: () => ({
    attributes: {},
    endpoint: Nova.config.productInformationEndpoint,
    loading: true,
  }),

  mounted() {
    this.initializeComponent()
  },

  methods: {
    async initializeComponent() {
      await this.getAttributes()

      this.loading = false
    },

    async getAttributes() {
      if (!this.endpoint || !this.gtin) {
        return
      }

      try {
        const {
          data: { attributes },
        } = await Nova.request().get(`${this.endpoint}/${this.gtin}`)

        return (this.attributes = attributes)
      } catch (error) {
        this.fields.map(field => {
          this.attributes[field.name] = '—'
        })
      }
    },
  },

  render() {
    return this.$scopedSlots.default({
      attributes: this.attributes,
      loading: this.loading,
    })
  },
}
</script>
