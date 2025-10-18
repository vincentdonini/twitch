import {createSlice, createAsyncThunk} from '@reduxjs/toolkit';
import {User} from '@common/api/domain/User';
import {UserService} from '@common/api/application/UserService';
import {AppDispatch} from '@common/stores/stores';
import {useDispatch} from "react-redux";

interface UserState {
    currentUser: User | null;
    status: 'idle' | 'loading' | 'failed';
}

const initialState: UserState = {
    currentUser: null,
    status: 'idle',
};

// thunk qui appelle UserService
export const fetchCurrentUser = createAsyncThunk<User, void, { dispatch: AppDispatch }>(
    'user/fetchCurrentUser',
    async (_, {rejectWithValue}) => {
        try {
            const userService = new UserService();
            return await userService.fetchCurrentUser();
        } catch (err) {
            return rejectWithValue(err);
        }
    }
);

const userSlice = createSlice({
    name: 'user',
    initialState,
    reducers: {
        logout: (state) => {
            state.currentUser = null;
            localStorage.removeItem('token');
            localStorage.removeItem('refresh_token');
        },
    },
    extraReducers: (builder) => {
        builder
            .addCase(fetchCurrentUser.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(fetchCurrentUser.fulfilled, (state, action) => {
                state.status = 'idle';
                state.currentUser = action.payload;
            })
            .addCase(fetchCurrentUser.rejected, (state) => {
                state.status = 'failed';
                state.currentUser = null;
            });
    },
});

export const {logout} = userSlice.actions;
export default userSlice.reducer;
