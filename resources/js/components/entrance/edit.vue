<template>
    <div>
      <div class="row">
        <div class="col-md-12">
          <div class="card card-stats card-raised boxtop">
            <div class="card-body">
              <div class="row">
                <div class="col-md-2 col-6">
                  <div class="statistics">
                    <div class="info">
                      <div class="icon icon-primary">
                        <i class="fa fa-exclamation" aria-hidden="true"></i>
                        
                      </div>
                      <h3 class="info-title">
                        {{ detaiData.no_ans }}
                      </h3>
                      <h6 class="stats-title">Trả Lời Sai</h6>
                    </div>
                  </div>
                </div>
                <div class="col-md-2 col-6">
                  <div class="statistics">
                    <div class="info">
                      <div class="icon icon-success">
                        <i class="fa fa-check" aria-hidden="true"></i>
                      </div>
                      <h3 class="info-title">
                        {{ detaiData.yes_ans }}
                      </h3>
                      <h6 class="stats-title">Trả Lời Đúng</h6>
                    </div>
                  </div>
                </div>
                <div class="col-md-2 col-6">
                  <div class="statistics">
                    <div class="info">
                      <div class="icon icon-info">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                      </div>
                      <h3 class="info-title">
                        {{ skipques  }}
                      </h3>
                      <h6 class="stats-title">Bỏ Qua</h6>
                    </div>
                  </div>
                </div>
                <div class="col-md-2 col-6">
                  <div class="statistics">
                    <div class="info">
                      <div class="icon icon-info">
                        <i class="fa fa-clock" aria-hidden="true"></i>
                      </div>
                      <h3 class="info-title">
                        {{ detaiData.time_minute  }} : {{ detaiData.time_second  }}
                      </h3>
                      <h6 class="stats-title">Thời Gian Làm Bài</h6>
                    </div>
                  </div>
                </div>
                <div class="col-md-2 col-6">
                  <div class="statistics">
                    <div class="info">
                      <div class="icon icon-info">
                        <i class="fa fa-flag" aria-hidden="true"></i>
                      </div>
                      <h3 class="info-title">
                        {{ detaiData.yes_ans }}/{{ skipquesall  }}
                      </h3>
                      <h6 class="stats-title">Kết Quả</h6>
                    </div>
                  </div>
                </div>
                <div class="col-md-2 col-6">
                  <div class="statistics">
                    <div class="info">
                      <div class="icon icon-info">
                        <i class="fa fa-flag" aria-hidden="true"></i>
                      </div>
                      <h3 class="info-title" v-if="detaiData.type == 'fulltest' ">
                        Full Test
                      </h3>
                      <h3 class="info-title" v-if="detaiData.type != 'fulltest' "">
                        Luyện Tập
                      </h3>
                      <h6 class="stats-title">Loại</h6>
                    </div>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="instructor-profile-right">
                      <div class="inner">
                        <div class="section-title text-start">
                            <h3 class="tit-phantic">Chi Tiết Bài Thi:</h3>
                            <div class="row">
                              <div class="col-md-6 col-6" :key="indextr" v-for="(item, indextr) in part">
                                <div >
                              <h3 class="tit-part">{{item.name}}</h3>
                              <div class="result-answers-list">
                                <div :key="index" v-for="(q, index) in item.questions">
                                <div class="result-answers-item">
                                    <span class="question-number"><strong>{{q.numerical_order}}</strong></span>
                                    <span>
                                    <span class="text-answerkey">{{q.ans}}</span> --
                                    <span v-if="getMyAns(q.id,q.numerical_order) === true">
                                      <span>
                                        <i class="mr-1 text-answerkeyae">{{getMyAnsResult(q.id,q.numerical_order)}} </i>  (Đáp án của học viên)
                                      </span>
                                    </span>
                                    <span v-else >
                                      <i class="mr-1 chuatraloi">Chưa Trả Lời</i>
                                    </span>
                                  <!-- @if ()
                                        @if (checkQuestionRight($ques->id,$result,$ques->numerical_order) == true)
                                        <i class="mr-1">{{getMyAnsResult($ques->id,$result,$ques->numerical_order)}}</i>
                                        <i class="ri-check-double-line" style="color: rgb(11, 194, 57); font-size: 20px;"></i>
                                        @else 
                                        <i class="text-line-through mr-1">{{getMyAnsResult($ques->id,$result,$ques->numerical_order)}}</i>
                                        <i class="ri-error-warning-fill" style="color: rgb(194, 11, 118); font-size: 20px;"></i>
                                        @endif
                                  @else
                                  <i class="mr-1">Chưa Trả Lời</i>
                                  @endif -->
                                    </span>
                                </div>
                              </div>
                            </div>
                          </div>
                              </div>
                            </div>
                            
                        </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  <script>
import { mapActions } from "vuex";
  
  export default {
    components: {
    },
    data() {
      return {
        detaiData:{
            id:this.$route.params.id_result,
        },
        part:[],
        allques:0,
        skipques:0,
        skipquesall:0,
        question:{},
        ac:""
      };
    },
    methods: {
    ...mapActions(["detailResult","editQuizExamId",'editQuizQuestionId']),
    getDetailResult(){
        this.loadings == true;
        this.detailResult({id:this.detaiData.id}).then(response => {
            this.loadings == false;
            this.detaiData = response.data;
            this.detaiData.result_json = JSON.parse(response.data.result_json);
            this.part = response.part;
            this.editQuizExamId({id:this.detaiData.exam_id}).then(response => {
                this.allques = response.data.questions.length;
                this.skipques = response.data.questions.length - this.detaiData.all_ans;
                this.skipquesall = response.data.questions.length;
                // this.list_result = response.data.resulttest;
            }).catch(error => {
                // this.loadings == false;
            })
            // this.list_result = response.data.resulttest;
        }).catch(error => {
            this.loadings == false;
        })
    },
     getMyAns(quetions_id,numerical_order) {
      var result_json_re = this.detaiData.result_json;
      for (const element of result_json_re) {
        if(element.numerical_order == numerical_order){
          return true;
        }
      }
    },
    getMyAnsResult(quetions_id,numerical_order){
      var result_json_re = this.detaiData.result_json;
      for (const element of result_json_re) {
        
        if(element.numerical_order == numerical_order){
          return element.result;
        }
      }
        
    }

    },
    mounted() {
      this.getDetailResult();
    //   this.editQuizExamIds();
  }
  };
  </script>
  <style>
  </style>
  