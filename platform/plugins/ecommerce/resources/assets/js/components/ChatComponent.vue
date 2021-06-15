<template>
    <div class="card">
        <div class="card-header">{{ otherUser.name }}</div>
        <div class="card-body">
            <div v-for="message in messages" v-bind:key="message.id">
                <div :class="{ 'text-right': message.author === '+13345390661' }">
                    <i>{{ message.date }}</i> <b>{{ message.body }}</b>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <input type="text" v-model="newMessage" class="form-control" placeholder="Type your message..." @keyup.enter="sendMessage"/>
        </div>
    </div>
</template>



<script>
export default {
    // name: "ChatComponent",
    props: {
        authUser: {
            type: Object,
            required: true
        },
        otherUser: {
            type: Object,
            required: true
        },
        messages: {
            type: Array
        },
        sid: {
            type: String
        },
    },
    data() {
        return {
            //messages: [],
            newMessage: "",
            channel: "",
            polling: null
        };
    },
    async created() {
        // const token = await this.fetchToken();
        // await this.initializeClient(token);
        /*await this.fetchMessages();*/
        this.pollData();
    },
    methods: {
        /*async fetchToken() {
            const {data} = await axios.post("/admin/orders/generate-token", {
                email: `${this.authUser.id}`
            });
            return data.token;
        },*/
        /*async fetchMessages() {
            this.messages = (await this.channel.getMessages()).items;
        },
        sendMessage() {
            this.channel.sendMessage(this.newMessage);
            this.newMessage = "";
        }*/

        /*async initializeClient(token) {
            const client = await Twilio.Chat.Client.create(token);
            client.on("tokenAboutToExpire", async () => {
                const token = await this.fetchToken();
                client.updateToken(token);
            });
            this.channel = await client.getChannelByUniqueName(
                `${this.authUser.id}-${this.otherUser.id}`
            );
            this.channel.on("messageAdded", message => {
                this.messages.push(message);
            });
        },*/

        async sendMessage() {
            let self = this;
            const {data} = await axios.post("/admin/chatings/send-sms", {
                sid: `${this.authUser.id}-${this.otherUser.id}`,
                author: '+13345390661', //self.otherUser.phone,
                body: self.newMessage,
            });
            this.messages = data.messages;
            this.newMessage = "";
        },

        async pollData () {
            this.polling = setInterval(async () => {
                const {data} = await axios.post("/admin/chatings/get-sms", {
                    sid: `${this.authUser.id}-${this.otherUser.id}`,
                });
                this.messages = data.messages;
            }, 10000)
        }
    },
    watch: {
        /*sid(value) {
            this.sid = value;
        },
        messages(value) {
            this.messages = value;
        },
        authUser(value) {
            this.authUser = value;
        },
        otherUser(value) {
            this.otherUser = value;
        }*/
    }
}
</script>
