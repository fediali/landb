<template>
    <div class="card">
        <div class="row heading">
            <div class="col-sm-2 col-md-1 col-xs-3 heading-avatar">
                <div class="heading-avatar-icon">
                    <img src="https://bootdey.com/img/Content/avatar/avatar6.png">
                </div>
            </div>
            <div class="col-sm-8 col-xs-7 heading-name">
                <a class="heading-name-meta">{{ otherUser.name }}
                </a>
            </div>
        </div>

        <div class="row message" id="conversation">

            <div class="row message-body">
                <div v-for="message in messages" v-bind:key="message.id" class="col-sm-12 message-main-receiver">
                   <div :class="(message.author !== '+13345390661')? 'sender':'receiver'">
                        <div class="message-text">
                            {{ message.body }}
                        </div>
                        <span class="message-time pull-right">
               {{ message.date }}
            </span>
                    </div>
                </div>
            </div>


        </div>

        <div class="row reply">
            <div class="col-sm-1 col-xs-1 reply-emojis">
                <i class="fa fa-smile fa-2x"></i>
            </div>
            <div class="col-sm-9 col-xs-9 reply-main">
                <input type="text" v-model="newMessage" class="form-control" placeholder="Type your message..."
                       @keyup.enter="sendMessage"/>
            </div>
            <div class="col-sm-1 col-xs-1 reply-recording">
                <i class="fa fa-microphone fa-2x" aria-hidden="true"></i>
            </div>
            <div class="col-sm-1 col-xs-1 reply-send">
                <i class="fa fa-paper-plane fa-2x" aria-hidden="true"></i>
            </div>
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

        async pollData() {
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
