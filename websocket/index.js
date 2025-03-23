import express from "express";
import { createServer } from "http";
import { Server } from "socket.io";

// Express application
const app = express();

// HTTP server
const httpServer = createServer(app);

// Socket.IO
const io = new Server(httpServer, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
        transports: ['websocket', 'polling'],
        credentials: true
    }
});

// WebSocket connection management
io.on("connection", (socket) => {
    console.log("🟢 Client connected:", socket.id);

    socket.onAny((event, ...args) => {
        console.log(`📡 Received an event: ${event}`, args);
    });

    socket.on("message", (data) => {
        console.log("📩 Message received:", data);

        // Send the message to ALL connected clients
        io.emit("response", { content: data, sender: socket.id });
    });

    socket.on("disconnect", () => {
        console.log("🔴 Client disconnected:", socket.id);
    });
});

// Server
const PORT = 4000;
httpServer.listen(PORT, () => {
    console.log(`🚀 WebSocket server listening at https://ws.twitch.woder.local/socket.io/ (port:${PORT})`);
});
