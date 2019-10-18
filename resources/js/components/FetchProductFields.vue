<script>
export default {
    data: () => ({
        fields: [],
        loading: true,
        endpoint: Nova.config.productInformationEndpoint,
    }),

    created() {
        this.initializeComponent();
    },

    methods: {
        async initializeComponent() {
            await this.getFields();

            this.loading = false;
        },

        async getFields() {
            if (!this.endpoint) {
                return;
            }

            try {
                const {
                    data: { fields },
                } = await Nova.request().get(this.endpoint);

                return (this.fields = fields);
            } catch (error) {
                //
            }
        },
    },

    render() {
        return this.$scopedSlots.default({
            fields: this.fields,
            loading: this.loading,
        });
    },
};
</script>
