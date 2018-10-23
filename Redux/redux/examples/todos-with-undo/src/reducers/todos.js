import undoable, { includeAction } from 'redux-undo'

const todo = (state, action) => {
  
  switch (action.type) {
    case 'ADD_TODO':
      return {
        id: action.id,
        text: action.text,
        completed: false
      }
    case 'TOGGLE_TODO':
      if (state.id !== action.id) {
        return {
          ...state,
          editing:0,
          saving:0,
          completed:false
        }
      }

      return {
        ...state,
        completed: !state.completed
      }
    case 'SAVE_TODO':
      if (state.id !== action.id) {
        return {
          ...state,
          saving:0,
          editing:0,
          completed: false
        }
      }

      return {
        ...state,
        text:action.text,
        saving:action.saving,
        editing:0,
        completed: false
      }
    case 'EDIT_TODO':
      if (state.id !== action.id) {
        return {
          ...state,
          editing:0,
          saving:0,
          completed:false
        }
      }

      return {
        ...state,
        id: action.id,
        text:action.text,
        editing:1,
        saving:0,
        completed: true
      }
    default:
      return state
  }
}

const todos = (state = [], action) => {
  switch (action.type) {
    case 'ADD_TODO':
      return [
        ...state,
        todo(undefined, action)
      ]
    case 'TOGGLE_TODO':
      return state.map(t =>
        todo(t, action)
      )
    case 'SAVE_TODO':
      return state.map(t =>
        todo(t, action)
      )
    case 'EDIT_TODO':
      return state.map(t =>
        todo(t, action)
      )
    default:
      return state
  }
}



const undoableTodos = undoable(todos, { filter: includeAction(['ADD_TODO', 'TOGGLE_TODO', 'SAVE_TODO', 'EDIT_TODO']) })

export default undoableTodos
