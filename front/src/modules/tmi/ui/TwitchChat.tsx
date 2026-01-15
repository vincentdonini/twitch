import {useSelector} from "react-redux";
import {RootState} from "@stores/stores";
import {useEffect} from "react";
import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";

const TwitchChat = () => {
    const messages = useSelector((state: RootState) => state.chat.messages);

    useEffect(() => {
        twitchBot.connect();
    }, []);

    return (
        <div>
            <h2>Chat Twitch</h2>
            <ul>
                {messages.map((msg, index) => (
                    <li key={index}>
                        <strong>{msg.username}:</strong> {msg.message}
                    </li>
                ))}
            </ul>
        </div>
    );
};

export default TwitchChat;
