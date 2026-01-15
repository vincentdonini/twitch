import React, {SyntheticEvent, useEffect, useState} from "react";
import {simulations} from "@modules/tmi/domain/handlers/SimulationsHandler";
import {RootState} from "@stores/stores";
import {useSelector} from "react-redux";
import {getUsers} from "@common/api/application/GetUsers";
import {User} from "@common/api/domain/User";
import {Accordion, AccordionDetails, AccordionSummary, Typography} from "@mui/material";

export default function AdminTools() {
    const simulate = (type: keyof typeof simulations, ...args: (boolean | string | undefined)[]) => {
        const simulation = simulations[type];
        if (typeof simulation === "function") {
            // eslint-disable-next-line @typescript-eslint/ban-ts-comment
            // @ts-expect-error
            simulation(...args);
        } else {
            console.warn(`Simulation pour "${type}" non trouvée.`);
        }
    };

    const simulateConfig = [
        {
            category: "Moderation",
            actions: [
                {
                    key: "clearchat",
                    label: "Clear Chat",
                    action: () => simulate("clearchat"),
                },
                {
                    key: "ban",
                    label: "Ban",
                    action: () => simulate("ban"),
                },
                {
                    key: "message",
                    label: "Message",
                    action: () => simulate("message"),
                },
            ],
        },
        {
            category: "Emotes",
            actions: [
                // {
                //     key: "komodoHype_allowed",
                //     label: `KomodoHype allowed "malusse"`,
                //     action: () => simulate("komodoHype", "malusse"),
                // },
                {
                    key: "komodoHype_allowed",
                    label: `KomodoHype allowed "manugraph"`,
                    action: () => simulate("komodoHype", "manugraph"),
                },
                {
                    key: "komodoHype_disallowed",
                    label: `KomodoHype disallowed`,
                    action: () => simulate("komodoHype", "bubulle"),
                },
                {
                    key: "wimjaGg",
                    label: `wimjaGg`,
                    action: () => simulate("wimjaGg"),
                },
                {
                    key: "wimjaKeur",
                    label: `wimjaKeur`,
                    action: () => simulate("wimjaKeur"),
                },
                {
                    key: "troisPointsDeSuspension",
                    label: "Trois Points de Suspension",
                    action: () => simulate("troisPointsDeSuspension"),
                },
            ],
        },
        {
            category: "Subscription",
            actions: [
                {
                    key: "subscription_prime",
                    label: "Subscription (Prime)",
                    action: () => simulate("subscription", true),
                },
                {
                    key: "subscription_full",
                    label: "Subscription (Full)",
                    action: () => simulate("subscription", false),
                },
                {
                    key: "resub_prime",
                    label: "Re-subscription (Prime)",
                    action: () => simulate("resub", true),
                },
                {
                    key: "resub_full",
                    label: "Re-subscription (Full)",
                    action: () => simulate("resub", false),
                },
            ],
        },
        {
            category: "Others",
            actions: [
                {
                    key: "cheer",
                    label: "Cheer",
                    action: () => simulate("cheer"),
                },
                {
                    key: "wzbotFollowMessage",
                    label: "WZBot follow message",
                    action: () => simulate("wzbotFollowMessage"),
                },
            ],
        },
        {
            category: "(WIP) - Duel",
            actions: [
                {
                    key: "duelMessage",
                    label: "Duel message",
                    action: () => simulate("duelMessage"),
                },
                {
                    key: "acceptDuelMessage",
                    label: "Accept duel message",
                    action: () => simulate("acceptDuelMessage"),
                },
            ],
        },
    ];

    const alerts = useSelector((state: RootState) => state.alertQueue.alerts);
    const sounds = useSelector((state: RootState) => state.soundQueue.sounds);

    // const [users, setUsers] = useState<User[]>([]);
    // useEffect(() => {
    //     getUsers().then((users) => {
    //         setUsers(users);
    //     });
    // }, []);

    const [expanded, setExpanded] = useState<string | false>('panel1');

    const handleChange =
        (panel: string) => (event: SyntheticEvent, newExpanded: boolean) => {
            setExpanded(newExpanded ? panel : false);
        };

    return (
        <div className="p-6 bg-black text-white rounded-xl shadow-lg max-w-lg mx-auto absolute right-0 top-0 z-[9999]">
            <div className="flex flex-col gap-5">
                <h2 className="text-xl font-bold pb-4">
                    🛠️ Admin Tools - Event Simulator
                </h2>
                <h3 className="text-lg font-bold pb-4">📋 Active music</h3>
                <h3 className="text-lg font-bold pb-4">📋 List of Actions</h3>
                <div>
                    {simulateConfig.map((group, index) => (
                        <Accordion
                            key={index}
                            expanded={expanded === `panel-${index}`}
                            onChange={handleChange(`panel-${index}`)}
                        >
                            <AccordionSummary
                                aria-controls={`panel-${index}-content`}
                                id={`panel-${index}-header`}
                            >
                                <Typography component="span">{group.category}</Typography>
                            </AccordionSummary>
                            <AccordionDetails>
                                <div className="flex flex-col gap-2">
                                    {group.actions.map(action => (
                                        <button
                                            key={action.key}
                                            onClick={action.action}
                                            className="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-md"
                                        >
                                            Simulate an &#34;{action.label.toUpperCase()}&#34;
                                        </button>
                                    ))}
                                </div>
                            </AccordionDetails>
                        </Accordion>
                    ))}
                </div>

                <div className="flex flex-col gap-2">
                    <div>
                        <h3 className="text-lg font-bold pb-4">📋 List of Events</h3>
                        <ul>
                            {alerts.map((alert, idx) => (
                                <li key={idx}>
                                    {alert.username} did an {alert.type} at {new Date(alert.timestamp).toLocaleTimeString()}
                                </li>
                            ))}
                        </ul>
                    </div>

                    <div>
                        <h3 className="text-lg font-bold pb-4">📋 List of Sounds</h3>
                        <ul>
                            {sounds.map((sound, idx) => (
                                <li key={idx}>
                                    URL: {sound.url} with a volume of {sound.volume} at {new Date(sound.timestamp).toLocaleTimeString()}
                                </li>
                            ))}
                        </ul>
                    </div>

                    {/*<ul>*/}
                    {/*    {users.map((user) => (*/}
                    {/*        <li key={user.id}>*/}
                    {/*            <h3>Email: {user.email}</h3>*/}
                    {/*            <p>Nom: {user.firstName} {user.lastName}</p>*/}
                    {/*            <p>Rôles: {user.roles.join(', ')}</p>*/}
                    {/*        </li>*/}
                    {/*    ))}*/}
                    {/*</ul>*/}
                </div>
            </div>
        </div>
    );
}